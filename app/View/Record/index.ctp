
<div class="row-fluid">
	<table class="table table-bordered" id="table_records">
		<thead>
			<tr>
				<th>ID</th>
				<th>NAME</th>	
			</tr>
		</thead>
	</table>
</div>
<?php $this->start('script_own')?>
<script>
$(document).ready(function(){
	var table = $("#table_records").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "/app/webroot/index.php/Record/lists",
	});
})
</script>
<?php $this->end()?>