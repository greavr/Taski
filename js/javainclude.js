$(document).ready(function() {
	//ACCORDION BUTTON ACTION	
	$('div.accordionButton').click(function() {
		$('div.accordionContent').slideUp('normal');	
		$(this).next().slideDown('normal');
	});
 
	//HIDE THE DIVS ON PAGE LOAD	
	$("div.accordionContent").hide();
	$("div.portlet-content").hide();

	//Accordion mouse hover
	$("div.accordionButton").hover(
	function() {
     $(this).addClass("highlight");
	},
	function(){
		$(this).removeClass("highlight");
	});
   
   //Task Porlet
   ////Expand/Hide Task
	$( ".portlet-header .tooglesize" ).click(function() {
			$( this ).toggleClass( "shrink" ).toggleClass( "expand" );
			$( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
	});
	
	
	////Clickable Div
	$('.portlet-content').click(function(){
		var str = this.getAttribute('id');
		str = window.location.pathname + "?tid=" + str.substring(3);
		window.location = str;
	});
	
	////Task Completed
	$('.taskcompleted').click(function(){
		window.location = window.location.pathname + "?CompID=" + this.getAttribute('value');
	});
        
        //Settings
        ////Tasklists Edit
        $(".EditTaskList").click(function () {
                var tlid = this.getAttribute('tlid');
                var tasktitle = this.getAttribute('tasktitle');
                $("#tlTitle").attr('value', tasktitle);
                $("#tlid").attr('value', tlid)
        });
        
        ////Delete TaskList
	$(".DeleteTaskList").click(function () {
		var answer = confirm("Are you sure you want to delete this tasklist?")
		if (answer){
			var str = this.getAttribute('tlid');
			str = window.location.pathname + "?DeleteTaskList=" + str;
			window.location = str;
		}
	});
	
	//New Task
	////Date Picker
	Date.format = 'yy-mm-dd';
	$( "#TaskDueDate" ).datepicker({ dateFormat: 'yy-mm-dd', minDate: 0 });
		
	////Delete Button Click
	$("#DeleteButton").click(function () {
		var answer = confirm("Are you sure you want to delete this task?")
		if (answer){
			var str = this.getAttribute('tid');
			str = window.location.pathname + "?deletetid=" + str;
			window.location = str;
		}
	});
	
	////New Button Click
	$("#New").click(function () {
		window.location = window.location.pathname;
	});

	////Dragable
	$(function() {
		$("#newtask").draggable();
	});
	
	//Attachment
	$("#attachmentheader").click(function () {
		$('#attachments').toggle();
	});
        
        //DeleteAttachment
        $(".DeleteAttachment").click(function (){
            var answer = confirm("Are you sure you want to delete this attachment?")
		if (answer){
			var thistid = this.getAttribute('tid');
                        var thisaid = this.getAttribute('aid');
			str = window.location.pathname + "?DeleteAttachmentID=" + thisaid + "&tid=" + thistid;
			window.location = str;
		}
        });
        
 });
 