<?php
    
    App::uses('Sanitize', 'Utility');
    
	class FormatController extends AppController{
		
		public function q1(){
			
			$this->setFlash('Question: Please change Pop Up to mouse over (soft click)');
				
			
// 			$this->set('title',__('Question: Please change Pop Up to mouse over (soft click)'));
		}
        
        public function submission() {
            
            $selected = Sanitize::clean(trim($this->request->data['type'])); 
            $message = "<p>You have selected \"{$selected}\"</p>";
            
            if (empty($selected)) {
                $message = "<p>Sorry, we did not received any input from you. </p>";
            }
            
            $this->response->body($message);
            
            return $this->response;
        }
		
		public function q1_detail(){

			$this->setFlash('Question: Please change Pop Up to mouse over (soft click)');
				
			
			
// 			$this->set('title',__('Question: Please change Pop Up to mouse over (soft click)'));
		}
		
	}