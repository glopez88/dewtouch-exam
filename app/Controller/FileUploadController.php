<?php

App::uses('Validation', 'Utility');

class FileUploadController extends AppController {
    
	public function index() {
		$this->set('title', __('File Upload Answer'));

		$file_uploads = $this->FileUpload->find('all');
		$this->set(compact('file_uploads'));
	}
    
    public function import() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException;
        }
        
        $uploadFile = $this->request->data['file'];
        
        if (
            $uploadFile['error'] || 
            !is_uploaded_file($uploadFile['tmp_name']) || 
            !Validation::mimeType($uploadFile, array('text/csv', 'text/plain')) || 
            !Validation::extension($uploadFile['name'], array('csv'))
        ) {
            $this->response->location('/app/webroot/index.php/FileUpload?invalid=1');
            return $this->response;
        }
            
        // making file os compatible
        ini_set('auto_detect_line_endings', true);

        $lines = file($uploadFile['tmp_name']);
        $data = array(); 
        
        // we start at index 1 to skip the csv headings
        for($i = 1; $i < count($lines); $i++) { 
           $row = str_getcsv($lines[$i]); 
           
           $data[] = array(
               'name' => $row[0],
               'email' => $row[1]
           );
        }
        
        if ($data) {
            $this->FileUpload->saveMany($data);
        }
        
        // now we redirect to the index page 
        $this->response->location('/app/webroot/index.php/FileUpload?imported=' . (!empty($data) ? 1 : 0 ));
        
        return $this->response;
    }
}