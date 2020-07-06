<?php
	class RecordController extends AppController{
        
        public $components = array('RequestHandler');
        
        protected static $sortMapping = array('id', 'name');
        
		public function index() {
            // commented lines below, they are not needed anymore
            // ini_set('memory_limit','256M');
			// set_time_limit(0);
            
			$this->setFlash('Listing Record page too slow, try to optimize it.');
			
			$this->set('title',__('List Record'));
		}
        
        public function lists() {
            $recordsPerPage = abs(intval($this->request->query('iDisplayLength')));
            $sortColumnIndex = abs(intval($this->request->query('iSortCol_0')));
            $sortColumnIndex = !isset(self::$sortMapping[$sortColumnIndex]) ? 0 : $sortColumnIndex; 
            $orientation = strtolower($this->request->query('sSortDir_0')); 
            
            $order = self::$sortMapping[$sortColumnIndex];
            $orientation = in_array($orientation, array('asc', 'desc')) ? $orientation : 'asc';
            
            $criteria = array(
                'limit' => $recordsPerPage, 
                'offset' => abs(intval($this->request->query('iDisplayStart'))),
                'order' => array("Record.{$order} {$orientation}"),
                'conditions' => array()
            );
            
            if (!empty($this->request->query('sSearch'))) {
                $criteria['conditions'] = array('Record.name LIKE' => "%{$this->request->query('sSearch')}%");
            }
            
            // we only need the conditions to get the display records count
            $totalDisplayRecords = $this->Record->find('count', array('conditions' => $criteria['conditions'])); 
            $totalRecords = $this->Record->find('count');
            $records = $this->Record->find('all', $criteria);
            
            $data = array();
            
            foreach ($records as $record) {
                $data[] = array(
                    $record['Record']['id'], 
                    $record['Record']['name'],
                );
            }
            
            $this->response->type('application/json');
            $this->response->body(json_encode(
                array(
                    'aaData' => $data, 
                    'iTotalRecords' => $totalRecords,
                    'iTotalDisplayRecords' => $totalDisplayRecords,
                )
            ));
            
            return $this->response;
        }
		
		
// 		public function update(){
// 			ini_set('memory_limit','256M');
			
// 			$records = array();
// 			for($i=1; $i<= 1000; $i++){
// 				$record = array(
// 					'Record'=>array(
// 						'name'=>"Record $i"
// 					)			
// 				);
				
// 				for($j=1;$j<=rand(4,8);$j++){
// 					@$record['RecordItem'][] = array(
// 						'name'=>"Record Item $j"		
// 					);
// 				}
				
// 				$this->Record->saveAssociated($record);
// 			}
			
			
			
// 		}
	}