<div class="row-fluid">
	<div class="alert alert-info">
		<h3>Migration - Data Importer</h3>
	</div>
    
    <?php if ($this->request->query('imported') == 1): ?>
        <div class="alert alert-success">
    		<p>File imported successfully.</p>
    	</div>
    <?php endif; ?>
    
    <?php if ($this->request->query('invalid') == 1): ?>
        
        <?php 
            $message = '';
            switch($this->request->query('errType')) { 
                case 'file':
                    $message = 'Please upload a valid excel file.';
                    break;
                
                case 'size':
                    $message = 'File must not be over 10MB.';
                    break;
            } 
        ?>
        
        <?php if ($message): ?>
            <div class="alert alert-danger">
        		<p><?php echo $message; ?></p>
        	</div>
        <?php endif; ?>
        
    <?php endif; ?>
    
    <p>You can import the data using the form below. </p>
    
    <?php
        echo $this->Form->create(false, array('action' => '/import', 'type'=>'file'));
        echo $this->Form->input('file', array('label' => 'File Upload', 'type' => 'file'));
        echo $this->Form->submit('Upload', array('class' => 'btn btn-primary'));
        echo $this->Form->end();
    ?>
</div>