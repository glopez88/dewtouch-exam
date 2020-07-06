<?php
	class OrderReportController extends AppController{

		public function index(){

			$this->setFlash('Multidimensional Array.');

			$this->loadModel('Order');
			$orders = $this->Order->find('all',array('conditions'=>array('Order.valid'=>1),'recursive'=>2));
			// debug($orders);exit;

			$this->loadModel('Portion');
			$portions = $this->Portion->find('all',array('conditions'=>array('Portion.valid'=>1),'recursive'=>2));
			// debug($portions);exit;
            
            /*
			// To Do - write your own array in this format
			$order_reports = array('Order 1' => array(
										'Ingredient A' => 1,
										'Ingredient B' => 12,
										'Ingredient C' => 3,
										'Ingredient G' => 5,
										'Ingredient H' => 24,
										'Ingredient J' => 22,
										'Ingredient F' => 9,
									),
								  'Order 2' => array(
								  		'Ingredient A' => 13,
								  		'Ingredient B' => 2,
								  		'Ingredient G' => 14,
								  		'Ingredient I' => 2,
								  		'Ingredient D' => 6,
								  	),
								);
                                
                                */
                               
            $itemPartsMapping = array();
            
            /*
            the mapping looks similar to this: 
            $itemPartsMapping = array(
                <itemId> => array(
                     <partId> => array( 
                        'name' => 'Ingredient A', 
                        'value' => 6
                    ), 
                )
            );
            */
            
            // creating map
            foreach ($portions as $portion) {
                $itemId = $portion['Item']['id'];
                
                if (!isset($itemPartsMapping[$itemId])) {
                     $itemPartsMapping[$itemId] = array();
                }
                
                if (isset($portion['PortionDetail'])) {
                     foreach ($portion['PortionDetail'] as $detail) {
                         $itemPartsMapping[$itemId][$detail['part_id']] = array(
                             'name' => $detail['Part']['name'], 
                             'value' => (int) $detail['value']
                         );
                     }
                }
            }
             
             $order_reports = []; 
             
             foreach ($orders as $order) {
                 $orderKey = $order['Order']['name'];
                 
                 if (!isset($order_reports[$orderKey])) {
                     $order_reports[$order['Order']['name']] = array();
                 }
                 
                 foreach ($order['OrderDetail'] as $orderDetail) {
                     $item = $orderDetail['Item']; 
                     
                     foreach ($itemPartsMapping[$item['id']] as $part) {
                         if (!isset($order_reports[$orderKey][$part['name']])) {
                             $order_reports[$orderKey][$part['name']] = 0;
                         }
                         
                         $order_reports[$orderKey][$part['name']] += $part['value'];
                     }
                 }
                 
                 ksort($order_reports[$orderKey]);
             }

			// ...

			$this->set('order_reports',$order_reports);

			$this->set('title',__('Orders Report'));
		}

		public function Question(){

			$this->setFlash('Multidimensional Array.');

			$this->loadModel('Order');
			$orders = $this->Order->find('all',array('conditions'=>array('Order.valid'=>1),'recursive'=>2));

			// debug($orders);exit;

			$this->set('orders',$orders);

			$this->loadModel('Portion');
			$portions = $this->Portion->find('all',array('conditions'=>array('Portion.valid'=>1),'recursive'=>2));
				
			// debug($portions);exit;

			$this->set('portions',$portions);

			$this->set('title',__('Question - Orders Report'));
		}

	}