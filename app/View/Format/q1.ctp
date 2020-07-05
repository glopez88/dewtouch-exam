
<div id="message1">


<?php 
    echo $this->Form->create(false, array(
        'action' => '/submission',
        'id'=>'form_type', 
        'type'=>'file', 
        'class'=>'',
        'method'=>'POST', 
        'autocomplete'=>'off',
        'inputDefaults'=>array(
        		'label'=>false,
                'div'=>false,
                'type'=>'text',
                'required'=>false
            )
        )
    ) 
?>
	
<?php echo __("Hi, please choose a type below:")?>
<br><br>

<?php $options_new = array(
 		'Type1' => __('<span class="showTooltip" data-id="dialog_1" style="color:blue">Type1</span><div id="dialog_1" class="hide tooltip-content" title="Type 1">
 				<span style="display:inline-block"><ul><li>Description .......</li>
 				<li>Description 2</li></ul></span>
 				</div>'),
		'Type2' => __('<span class="showTooltip" data-id="dialog_2" style="color:blue">Type2</span><div id="dialog_2" class="hide tooltip-content" title="Type 2">
 				<span style="display:inline-block"><ul><li>Desc 1 .....</li>
 				<li>Desc 2...</li></ul></span>
 				</div>')
		);?>

<?php echo $this->Form->input('type', array('legend'=>false, 'type' => 'radio', 'options'=>$options_new,'before'=>'<label class="radio line notcheck">','after'=>'</label>' ,'separator'=>'</label><label class="radio line notcheck">'));?>

<?php echo $this->Form->button('Save', array('type' => 'submit', 'style' => 'margin-top: 20px;')); ?>

<?php echo $this->Form->end();?>

</div>

<style>
.showDialog:hover{
	text-decoration: underline;
}

#message1 .radio{
	vertical-align: top;
	font-size: 13px;
    position: relative;
}

#message1 .radio ul li {
    list-style-type: circle;
}

.control-label{
	font-weight: bold;
}

.wrap {
	white-space: pre-wrap;
}

.ui-tooltip {
	background: #ffffff;
	border: 1px solid #a1a1a1 !important;
    box-shadow: none;
    width: 200px !important;
    -webkit-box-shadow: 0px 20px 28px -23px rgba(143,139,143,1);
    -moz-box-shadow: 0px 20px 28px -23px rgba(143,139,143,1);
    box-shadow: 0px 20px 28px -23px rgba(143,139,143,1);
}

.ui-tooltip:after, .ui-tooltip:before {
    right: 100%;
	top: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
}

.ui-tooltip:after {
    border-color: rgba(255, 255, 255, 0);
	border-right-color: #ffffff;
	border-width: 5px;
	margin-top: -5px;
}

.ui-tooltip:before {
    border-color: rgba(161, 161, 161, 0);
	border-right-color: #a1a1a1;
	border-width: 6px;
	margin-top: -6px;
}

</style>

<?php $this->start('script_own')?>
<script>

$(document).ready(function(){
    // required due to bootstrap tooltip and jquery ui tooltip conflict
    $.fn.tooltip.noConflict();
    
   $(document).tooltip({
        items: ".showTooltip",
        position: { 
            my: 'left center', 
            at: 'right+20 center'
        },
        content: function() {
            return $(this).next().html();
        }
   });

})


</script>
<?php $this->end()?>