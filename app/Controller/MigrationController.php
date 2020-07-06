<?php
    require(APP . 'Vendor/PHPExcel/Classes/PHPExcel/IOFactory.php');
    
    App::uses('Validation', 'Utility');
    
	class MigrationController extends AppController{
        
        protected static $allowedTypes = array(
            'application/vnd.ms-excel', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        
        protected static $extensions = array('xls', 'xlsx');
        
        protected static $maxUploadSize = 10000000; // 10mb max
        
        /**
         * list of the known or columns we need to read
         *
         * @var array
         */
        protected static $tableHeadings = array(
            'Date',
            'Ref No.',
            'Member Name',
            'Member No', 
            'Member Pay Type',
            'Member Company', 
            'Payment By',
            'Batch No',
            'Receipt No', 
            'Cheque No',
            'Payment Description',
            'Renewal Year',
            'subtotal',
            'totaltax',
            'total'
        );
        
        /**
         * contains the information on what index the columns are located
         * this is useful to know even if the columns are interchanged,
         * we can still determine what information type they hold
         *
         * @var array
         */
        protected static $indexHeadingMapping = array();
        
        /**
         * the mapping between the member attribute and the column in the sheet
         *
         * @var array
         */
        protected static $membersHeadingMapping = array(
            'no'        => 'Member No', 
            'name'      => 'Member Name',
            'company'   => 'Member Company', 
            
        );
        
        /**
         * the mapping between the transaction attribute and the column in the sheet
         *
         * @var array
         */
        protected static $transactionHeadingMapping = array(
            'member_name' => 'Member Name', 
            'member_paytype' => 'Member Pay Type',
            'member_company' => 'Member Company',
            'date' => 'Date',
            'ref_no' => 'Ref No.',
            'receipt_no' => 'Receipt No',
            'payment_method' => 'Payment By',
            'batch_no' => 'Batch No',
            'cheque_no' => 'Cheque No',
            'payment_type' => 'Payment Description',
            'renewal_year' => 'Renewal Year',
            'subtotal' => 'subtotal',
            'tax' => 'totaltax',
            'total' => 'total',
        );
        		
		public function q1(){
			
			$this->setFlash('Question: Migration of data to multiple DB table');
				
			
// 			$this->set('title',__('Question: Please change Pop Up to mouse over (soft click)'));
		}
		
		public function q1_instruction(){

			$this->setFlash('Question: Migration of data to multiple DB table');
				
			
			
// 			$this->set('title',__('Question: Please change Pop Up to mouse over (soft click)'));
		}
        
        public function index() {            
            $this->set('title',__('Data Migration Tool'));
        }
		
        public function import() {
            if (!$this->request->is('post')) {
                throw new MethodNotAllowedException;
            }
            
            $this->loadModel('Member');
            $this->loadModel('Transaction');
            $this->loadModel('TransactionItem');
            
            $uploadFile = $this->request->data['file'];
            
            if (
                $uploadFile['error'] || 
                !is_uploaded_file($uploadFile['tmp_name']) || 
                !Validation::mimeType($uploadFile, self::$allowedTypes) || 
                !Validation::extension($uploadFile['name'], self::$extensions)
            ) {
                $this->response->location('/app/webroot/index.php/Migration?invalid=1&errType=file');
                return $this->response;
            }
            
            if(!Validation::fileSize($uploadFile, '<=', self::$maxUploadSize)) {
                $this->response->location('/app/webroot/index.php/Migration?invalid=1&errType=size');
                return $this->response;
            }
            
            $data = $this->parseFile($uploadFile['tmp_name']);
            
            $this->response->location('/app/webroot/index.php/migration?imported=1');
            
            return $this->response;
        }
        
        private function parseFile($file) {
            $inputFileType = PHPExcel_IOFactory::identify($file);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($file);
            
            $sheet = $objPHPExcel->getSheet(0); 
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();
            
           $this->mapHeadings($sheet);
            
            //  Loop through each row of the worksheet in turn
            // we skip the table heading so we need to start at index 2
            for ($row = 2; $row <= $highestRow; $row++) { 
                try {
                    //  Read a row of data into an array
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
                    
                    $member = $this->createMember($sheet, $row); 
                    $transaction = $this->createTransaction($sheet, $row, $member); 
                    $transactionItem = $this->createTransactionItem($sheet, $row, $transaction);
                } catch(MissingModelException $e) {
                    // logging unexpected error here
                    CakeLog::error("Error: {$e->getMessage()} \n Trace: {$e->getTraceAsString()}");
                }
            }
            
        }
        
        /**
         * This maps the table headings on where a column is at index
         * We won't have to worry later on if the order of the columns were interchanged.
         * Column Heading/Title will always be mapped to its correct model attribute
         *
         * @param $sheet 
         */
        private function mapHeadings($sheet) 
        {
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();
            
            // mapping table headings 
            $headings = $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false);
            
            foreach($headings[0] as $colIndex => $value) {
                if (!in_array($value, self::$tableHeadings)) {
                    continue; 
                }
                
                self::$indexHeadingMapping[$colIndex] = $value;
            }
        }
        
        private function createMember($sheet, $rowIndex) 
        {
            $data = array(
                'company' => null,
            ); 
            
            foreach (self::$membersHeadingMapping as $modelAttrKey => $tableColName) {
                $colIndex = array_search($tableColName, self::$indexHeadingMapping);
                $value = $sheet->getCellByColumnAndRow($colIndex, $rowIndex)->getFormattedValue();
                
                if ($tableColName === 'Member No') {
                    list ($type, $no) = explode(' ', $value); 
                    
                    $data['type'] = trim($type);
                    // restricting no to numeric chars only
                    $data['no'] = preg_replace('/[^0-9]/', '', $no); 
                    
                    continue;
                }
                
                $data[$modelAttrKey] = $value;
            }
            
            $member = $this->Member->find('first', array(
                'conditions' => array(
                    'Member.type' => $data['type'],
                    'Member.no' => $data['no']
                )
            ));
            
            if ($member) {
                return $member;
            }
            
            $this->Member->create($data); 
            $this->Member->save();
            
            return $this->Member->findById($this->Member->getLastInsertId());
        }
        
        private function createTransaction($sheet, $rowIndex, $member) 
        {
            if (!$member) {
                throw new MissingModelException('Cannot create a transaction on without a member');
            }
            
            $data = array(
                'member_id' => $member['Member']['id'],
                // setting some nullable fields to null
                'member_company' => null, 
                'batch_no' => null, 
                'cheque_no' => null, 
            ); 
            
            foreach (self::$transactionHeadingMapping as $modelAttrKey => $tableColName) {
                $colIndex = array_search($tableColName, self::$indexHeadingMapping);
                $value = $sheet->getCellByColumnAndRow($colIndex, $rowIndex)->getFormattedValue();
                
                if ($tableColName == 'Date') {
                    $date = DateTime::createFromFormat('n/j/Y', $value);
                    
                    $data['date'] = $date->format('Y-m-d');
                    $data['year'] = $date->format('Y');
                    $data['month'] = $date->format('n');
                    
                    continue;
                }
                
                $data[$modelAttrKey] = $value;
            }
            
            $transaction = $this->Transaction->find('first', array(
                'conditions' => array(
                    'Transaction.member_id' => $member['Member']['id'],
                    'Transaction.receipt_no' => $data['receipt_no']
                )
            ));
            
            if ($transaction) {
                return $transaction;
            }
            
            $this->Transaction->create($data); 
            $this->Transaction->save();
            
            return $this->Transaction->findById($this->Transaction->getLastInsertId());
        }
        
        private function createTransactionItem($sheet, $rowIndex, $transaction) 
        {
            if (!$transaction) {
                throw new MissingModelException('Cannot create a transaction item on without a transaction');
            }
            
            $transactionItem = $this->TransactionItem->find('first', array(
                'conditions' => array('TransactionItem.transaction_id' => $transaction['Transaction']['id'])
            ));
            
            if ($transactionItem) {
                return $transactionItem;
            }
            
            $data = array(
                'transaction_id' => $transaction['Transaction']['id'], 
                'table' => 'Member', 
                'table_id' => 1, 
                'quantity' => 1, 
                'unit_price' => $transaction['Transaction']['subtotal'],
                'sum' => $transaction['Transaction']['subtotal'],
            );
            
            if ($transaction['Transaction']['payment_type']) {
                $data['description'] = "Being payment for: \n" . $transaction['Transaction']['payment_type'];
            }
            
            $this->TransactionItem->create($data); 
            $this->TransactionItem->save();
            
            return $this->TransactionItem->findById($this->TransactionItem->getLastInsertId());
        }
	}