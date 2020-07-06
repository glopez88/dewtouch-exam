<div class="alert  ">
<button class="close" data-dismiss="alert"></button>
Question: Advanced Input Field</div>

<p>
1. Make the Description, Quantity, Unit price field as text at first. When user clicks the text, it changes to input field for use to edit. Refer to the following video.

</p>


<p>
2. When user clicks the add button at left top of table, it wil auto insert a new row into the table with empty value. Pay attention to the input field name. For example the quantity field

<?php echo htmlentities('<input name="data[1][quantity]" class="">')?> ,  you have to change the data[1][quantity] to other name such as data[2][quantity] or data["any other not used number"][quantity]

</p>



<div class="alert alert-success">
<button class="close" data-dismiss="alert"></button>
The table you start with</div>

<table class="table table-striped table-bordered table-hover editable-table">
<thead>
<th><span id="add_item_button" class="btn mini green addbutton" onclick="addToObj=false">
    <i class="icon-plus"></i></span></th>
<th>Description</th>
<th>Quantity</th>
<th>Unit Price</th>
</thead>

<tbody>
    <tr>
    	<td width="10%" class="action"><span class="btn mini remove-row"><i class="icon-remove"></i></span></td>
    	<td width="50%"><textarea name="data[1][description]" class="m-wrap  description required" rows="2" ></textarea></td>
    	<td width="20%"><input type="number" name="data[1][quantity]" class=""></td>
    	<td width="20%"><input type="number" name="data[1][unit_price]"  class=""></td>
    </tr>

</tbody>

</table>


<p></p>
<div class="alert alert-info ">
<button class="close" data-dismiss="alert"></button>
Video Instruction</div>

<p style="text-align:left;">
<video width="78%"   controls>
  <source src="/video/q3_2.mov">
Your browser does not support the video tag.
</video>
</p>





<?php $this->start('script_own');?>
<style>
.editable-table textarea, 
.editable-table input {
    width: 100%;
    box-sizing: border-box;
    padding: 10px;
}

.editable-table input {
    height: 30px;
}

</style>
<script>
$(document).ready(function() {
    
    var TableEditableRows = (function() {
        var defaults = {
            table: 'table', 
            addButton: '.add-row'
        };
        
        var TableEditableRows = function(options) {
            this.options = $.extend({}, defaults, options);
            this.init(); 
        }
        
        return TableEditableRows;
    })();
    
    TableEditableRows.prototype.init = function() {
        this.$table = $(this.options.table);
        this.$addButton = $(this.options.addButton);
        this.$tbody = this.$table.find('tbody');
        
        this.toViewMode(); 
        this.events();
    }; 
    
    TableEditableRows.prototype.events = function() {
        var _this = this; 
        
        _this.$addButton.on('click', function() {
            console.log('adding row')
            _this.addRow();
        })
        
        _this.$tbody.on('click', 'td.view-mode', function(e) {
            console.log('to edit mode...')
            _this.toEditMode($(this));
        });
        
        _this.$tbody.on('blur', 'td :input', function(e) {
            console.log('to view mode...')
            var $col = $(this).parent();
            
            _this.toViewMode($col);
        });
        
        _this.$tbody.on('click', 'td .remove-row', function() {
            $(this).closest('tr').remove();
        });
    };
    
    TableEditableRows.prototype.addRow = function() {
        var _this = this; 
        var nextIndex = this.createNextIndex(); 
        var $row = this.createRow(nextIndex);
        
        $row.find('td:not(.action)').map(function(key, td) {
            _this.toViewMode($(td));
        });
        
        this.$tbody.append($row);
    };
    
    TableEditableRows.prototype.toViewMode = function($col) {
        if ($col) { // if column is present, only this will set to view mode
            var val = $col.find(':input').val(); 
            val = val.trim().length ? val : '&nbsp;';
            
            if ($col.find('.display-text').length == 0) {    
                $col.append('<span class="display-text" style="display: block;padding:5px;">' + val + '</span>');
            } else {
                $col.find('.display-text').html(val).show(); 
            }
            
            $col.find(':input').hide();
            $col.addClass('view-mode');
        } else { // otherwise, we need to set every columns of every row to view mode
            var $columns = this.$tbody.find('td:not(.action)');
            
            for (var i = 0; i < $columns.length; i++) {
                this.toViewMode($($columns[i]));
            }
        }
    };
    
    TableEditableRows.prototype.toEditMode = function($col) {
        $col.find('.display-text').hide(); 
        $col.find(':input').show();
        $col.find(':input').focus();
    };
    
    TableEditableRows.prototype.createNextIndex = function() {
        var nextIndex = this.$tbody.find('tr').length + 1; 
        var exists = false;
        
        while(this.$tbody.find('textarea[name="data[' + nextIndex + '][description]"]').length > 0) {
            nextIndex++;
        }
        
        return nextIndex;
    };
    
    TableEditableRows.prototype.createRow = function(index) {
        var html = [];
        
        html.push('<td width="10%" class="action"><span class="btn mini remove-row"><i class="icon-remove"></i></span></td>');
        html.push('<td width="50%"><textarea name="data[' + index + '][description]" class="m-wrap  description required" rows="2" ></textarea></td>');
        html.push('<td width="20%"><input type="number" name="data[' + index + '][quantity]" class=""></td>');
        html.push('<td width="20%"><input type="number" name="data[' + index + '][unit_price]"  class=""></td>');
        
        
        return $('<tr>' + html.join('') + '</tr>');
    };
    
    var editableTable = new TableEditableRows({
        table: '.editable-table', 
        addButton: '#add_item_button'
    });
    
    
});
</script>
<?php $this->end();?>

