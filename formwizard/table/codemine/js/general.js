
/*------------JQUERY onReady Functions ------------------*/
// GET Brackettype dropdown --------------
var newOptionsBT ='<option value=""></option>';
var newOptionsTT ='<option value=""></option>';

$(document).ready(function () {
	
	//Loading animation during ajax request ...
	$body = $("body");
	
	$(document).on({
		ajaxStart: function() { $body.addClass("loading");    },
		 ajaxStop: function() { $body.removeClass("loading"); }    
	});    


//Initialize Step0 dropdowns
onloadstep0();

// BRACKET type in form
	$.ajax({ // ajax call starts
	  url: 'ajaxfunctions.php?quoteaction=getBracketTypeOptions', // JQuery loads serverside.php
	  data: '',
	  dataType: 'json', // Choosing a JSON datatype
	  async: false, // Synchronous AJAX REQUESTTTTTTTTTTTTTTTTTTTTTTTT
	  success: function(data) // Variable data contains the data we get from serverside
	  {
		bdata = data;
				
		// Bracket type  building options for SELECT2
		for (var key in data) {
		  if (data.hasOwnProperty(key)) {
			newOptionsBT += '<option value="'+data[key]['Value']+'">'+data[key]['DisplayText']+'</option>';
		  }
		}
	  }
	});
	
// Timber type in form
	$.ajax({ // ajax call starts
	  url: 'ajaxfunctions.php?quoteaction=getTimberTypeOptions', // JQuery loads serverside.php
	  data: '',
	  dataType: 'json', // Choosing a JSON datatype
	  async: false, // Synchronous AJAX REQUESTTTTTTTTTTTTTTTTTTTTTTTT
	  success: function(data) // Variable data contains the data we get from serverside
	  {
		
		// Timber type  building options for SELECT2
		for (var key in data) {
		  if (data.hasOwnProperty(key)) {
			newOptionsTT += '<option value="'+data[key]['Value']+'">'+data[key]['DisplayText']+'</option>';
		  }
		}
	  }
	});
var noofsteps=0;
var isnoofstepschanged=0;

/*------------Form Wizard Functions ------------------*/
			
			$(function(){		
				$("#demoForm").formwizard({ 
				 	formPluginEnabled: true,
				 	validationEnabled: true,
				 	focusFirstInput : true,
				 	formOptions :{
						success: function(data){$("#status").fadeTo(500,1,function(){ $(this).html("You are now registered!").fadeTo(5000, 0); })},
						beforeSubmit: function(data){$("#data").html("data sent to the server: " + $.param(data));},
						dataType: 'json',
						resetForm: true
				 	}
				 }
				);
			
			var remoteAjax = {}; // empty options object

			$("#demoForm .step").each(function(){ // for each step in the wizard, add an option to the remoteAjax object...
				remoteAjax[$(this).attr("id")] = {
					url : "ajaxfunctions.php", // the url which stores the stuff in db for each step
					dataType : 'json',
					beforeSubmit: function(data){
						//$("#data").html("data sent to the server: " + $.param(data));
						},
					success : function(data){
					 			if(data){ //data is either true or false (returned from store_in_database.html) simulating successful / failing store
						 			//$("#data").append("    .... store done successfully");

									if (data["resultstep"] == '0')
									{
//										if((noofsteps=='0') || (noofsteps!=data["noofsteps"]))
										isnoofstepschanged = 1;
										noofsteps = data["noofsteps"];
										$("#archmaterial").select2("val", data['archmaterialid']).trigger("change");
										autopopulatefieldsstep1(data);
									}
									
									if (data["resultstep"] == '1')
									autopopulatefieldsstep2(data);
									
						 		}else{
						 			alert("Server-side validation returned errors, nothing was stored.");
						 		}
						 		
					 			return data; //return true to make the wizard move to the next step, false will cause the wizard to stay on the CV step (change this in store_in_database.html)
					 		}
					};
			});
			
			$(function(){
				// bind a callback to the step_shown event
				$("#demoForm").bind("step_shown", function(event, data){
					//alert(data.isBackNavigation);
					if(data.currentStep == 'step2')
					onloadstep2(data);
					if(data.currentStep == 'step3')
					onloadstep3(data);
					if(data.currentStep == 'step4')
					onloadstep4(data);
					if(data.currentStep == 'step5')
					onloadstep5(data);
				});
			});	
							
			$("#demoForm").formwizard("option", "remoteAjax", remoteAjax); // set the remoteAjax option for the wizard
  			});
			
/*------------Ending Form Wizard Functions ------------------*/

/*------------Step 1 onReady Functions ------------------*/

		    //Prepare jTable
			$('#StepTableContainer').jtable({
				title: 'Step Configuration',
				paging: true,
				footer: true,
				sorting: false,
				selecting: true, //Enable selecting
				multiselect: true, //Allow multiple selecting
				selectingCheckboxes: true, //Show checkboxes on first column
				//selectOnRowClick: false, //Enable this to only select using checkboxes
				messages: {
					addNewRecord: 'Add new Step',
					editRecord: 'Edit Step',
					deleteConfirmation: 'This Step will be deleted. Are you sure?',
				},
				actions: {
					listAction: 'JTableStepArrayActions.php?action=list',
					createAction: 'JTableStepArrayActions.php?action=create',
					updateAction: 'JTableStepArrayActions.php?action=update',
					deleteAction: 'JTableStepArrayActions.php?action=delete'
				},
				toolbar: {
					items: [{
						text: 'Delete Selected Step',
						click: function () {
							//perform your custom job...
							var $selectedStepRows = $('#StepTableContainer').jtable('selectedRows');
							$('#StepTableContainer').jtable('deleteRows', $selectedStepRows);
						}
					}]
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false
						},
					quoteid: {
						list: false,
						type: 'hidden',
						defaultValue: quoteid
						},
					steptag: {
						title: 'Tag',
						width :'5%'
						},
					length: {
						title: 'Length',
						inputClass: 'validate[required,custom[number]]',
						width :'6%',
						sorting: false,
						},
					width: {
						title: 'Width',
						inputClass: 'validate[required,custom[number]]',
						width :'6%',
						sorting: false
						},
				},
				//Initialize validation logic when a form is created
				formCreated: function (event, data) {
					data.form.validationEngine();
				},
				//Validate form when it is being submitted
				formSubmitting: function (event, data) {
				//	return data.form.validationEngine('validate');
				},
				//Dispose validation logic when form is closed
				formClosed: function (event, data) {
					data.form.validationEngine('hide');
					data.form.validationEngine('detach');
				},

				 
				recordsLoaded: function (event, data) {
					var totalcanopylength = 0;
					var totalcanopywidth = 0;
					
					$.each(data.records, function(index, record) {
						totalcanopylength += Number(record.length);
						if (totalcanopywidth < Number(record.width))
						totalcanopywidth = Number(record.width);
					});
					
					$("#totalcanopylength").html(totalcanopylength);
					$("#totalcanopywidth").html(totalcanopywidth);
					
				},
				
				
				//Register to selectionChanged event to hanlde events
				selectionChanged: function () {
					//Get all selected rows
					var $selectedRows = $('#StepTableContainer').jtable('selectedRows');
	 
					$('#steptable_message').empty();
					if ($selectedRows.length > 0) {
						//Show selected rows
						$selectedRows.each(function () {
							var record = $(this).data('record');
							$('#table_message').html('<div style="padding:5px">'+$selectedRows.length+' rows selected!</div>');
						});
					} else {
						//No rows selected
						$('#table_message').html('<div style="padding:5px">No rows selected!</div>');
					}
				},
				rowInserted: function (event, data) {},
								
			});
			
			//Load steps from server
			$('#StepTableContainer').jtable('load');
			// Adding an additional row at the end of the table for showing sum results
			$('#StepTableContainer table.jtable').append('<tfoot><tr><td class="cell" colspan="2">Overall :</td><td id="totalcanopylength"  class="sumcell"></td><td id="totalcanopywidth"  class="sumcell"></td></tr></tfoot>');
			// Adding a div into the footer panel of jTable with custom message
			$("#table_message").appendTo("#StepTableContainer .jtable-bottom-panel .jtable-left-area").addClass("filter_results");

/*------------Ending Step 1 onReady Functions ------------------*/

/*------------Additional Items Table onReady Functions ------------------*/

		    //Prepare jTable
			$('#AdditionalItemsContainer').jtable({
				title: 'Additional Items',
				paging: true,
				footer: true,
				sorting: false,
				selecting: true, //Enable selecting
				multiselect: true, //Allow multiple selecting
				selectingCheckboxes: true, //Show checkboxes on first column
				//selectOnRowClick: false, //Enable this to only select using checkboxes
				messages: {
					addNewRecord: 'Add new Item',
					editRecord: 'Edit Item',
					deleteConfirmation: 'This Item will be deleted. Are you sure?',
				},
				actions: {
					listAction: 'JTableAdditionalItemsArrayActions.php?action=list',
					createAction: 'JTableAdditionalItemsArrayActions.php?action=create',
					updateAction: 'JTableAdditionalItemsArrayActions.php?action=update',
					deleteAction: 'JTableAdditionalItemsArrayActions.php?action=delete'
				},
				toolbar: {
					items: [{
						text: 'Delete Selected Item',
						click: function () {
							//perform your custom job...
							var $selectedAdditionalItemRows = $('#AdditionalItemsContainer').jtable('selectedRows');
							$('#AdditionalItemsContainer').jtable('deleteRows', $selectedAdditionalItemRows);
						}
					}]
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false
						},
					quoteid: {
						list: false,
						type: 'hidden',
						defaultValue: quoteid
						},
					item: {
						title: 'Item',
						options: 'ajaxfunctions.php?quoteaction=getAdditionalItemOptions',
						width :'20%'
						},
					quantity: {
						title: 'Quantity',
						width :'6%',
						sorting: false,
						},
					specification: {
						title: 'Specification',
						width :'6%',
						sorting: false
						},
					cost: {
						title: 'Cost ($$$)',
						inputClass: 'validate[required,custom[number]]',
						width :'6%',
						sorting: false
						},
				},
				//Initialize validation logic when a form is created
				formCreated: function (event, data) {
					data.form.validationEngine();
				},
				//Validate form when it is being submitted
				formSubmitting: function (event, data) {
				//	return data.form.validationEngine('validate');
				},
				//Dispose validation logic when form is closed
				formClosed: function (event, data) {
					data.form.validationEngine('hide');
					data.form.validationEngine('detach');
				},

				 
				recordsLoaded: function (event, data) {
					var additionaltotalcost = 0;
					
					$.each(data.records, function(index, record) {
						additionaltotalcost += Number(record.cost);
					});
					
					$("#totaladditionalcost").html(additionaltotalcost);
					
				},
				
				
				//Register to selectionChanged event to hanlde events
				selectionChanged: function () {
					//Get all selected rows
					var $selectedRows = $('#AdditionalItemsContainer').jtable('selectedRows');
	 
					$('#additionaltable_message').empty();
					if ($selectedRows.length > 0) {
						//Show selected rows
						$selectedRows.each(function () {
							var record = $(this).data('record');
							$('#additionaltable_message').html('<div style="padding:5px">'+$selectedRows.length+' rows selected!</div>');
						});
					} else {
						//No rows selected
						$('#additionaltable_message').html('<div style="padding:5px">No rows selected!</div>');
					}
				},
				rowInserted: function (event, data) {},
								
			});
			
			//Load steps from server
			$('#AdditionalItemsContainer').jtable('load');
			// Adding an additional row at the end of the table for showing sum results
			$('#AdditionalItemsContainer table.jtable').append('<tfoot><tr><td></td><td></td><td></td><td class="cell" style="text-align:right">Total Additional Item Cost :</td><td id="totaladditionalcost"  style="background:#f0f0f0"></td></tr></tfoot>');
			// Adding a div into the footer panel of jTable with custom message
			$("#additionaltable_message").appendTo("#AdditionalItemsContainer .jtable-bottom-panel .jtable-left-area").addClass("filter_results");

/*------------Additional Items onReady Functions ------------------*/


/*------------Step 2 onReady Functions ------------------*/
			initializeDropDowns('1'); 
			
		    //Prepare jTable
			$('#FrontTableContainer').jtable({
				title: 'Section Configuration',
				paging: true,
				pageSize: 15,
				footer: true,
				sorting: true,
				defaultSorting: 'sectiontag ASC',
				selecting: true, //Enable selecting
				multiselect: true, //Allow multiple selecting
				selectingCheckboxes: true, //Show checkboxes on first column
				//selectOnRowClick: false, //Enable this to only select using checkboxes
				messages: {
					addNewRecord: 'Add new Section',
					editRecord: 'Edit Section',
					deleteConfirmation: 'This Section will be deleted. Are you sure?',
				},
				actions: {
					listAction: 'JTableFrontArrayActions.php?action=list',
					createAction: 'JTableFrontArrayActions.php?action=create',
					updateAction: 'JTableFrontArrayActions.php?action=update',
					deleteAction: 'JTableFrontArrayActions.php?action=delete'
				},
				toolbar: {
					items: [{
						text: 'Duplicate Selected Section',
						click: function () {
							//perform your custom job...
							//Get all selected rows
							var $selectedRows = $('#FrontTableContainer').jtable('selectedRows');
			 
							if ($selectedRows.length > 0) {
								//Show selected rows
								$selectedRows.each(function () {
									var record = $(this).data('record');
									//Copy Selected Records one by one
									$('#FrontTableContainer').jtable('addRecord', {
										record: {
											quoteid: record.quoteid,
											sectiontag: record.sectiontag,
											side: record.side,
											plength: record.plength,
											pheight: record.pheight,
											support: record.support,
											postposition: record.postposition,
											postmaterial: record.postmaterial,
											postbaseplate: record.postbaseplate,
											brackets: record.brackets,
											brackettype: record.brackettype,
											bracketspacing: record.bracketspacing,
											sectiontype: record.sectiontype,
											filltype: record.filltype,
											fabrictype: record.fabrictype,
											rollerblinds: record.rollerblinds,
											rollerfabric: record.rollerfabric,
										}

									});
								});
							} else {
								//No rows selected
								$('#table_message').append('<div style="padding:5px">No row selected!</div>');
							}
						}
					},{
						text: 'Delete Selected Section',
						click: function () {
							//perform your custom job...
							var $selectedRows = $('#FrontTableContainer').jtable('selectedRows');
							$('#FrontTableContainer').jtable('deleteRows', $selectedRows);
						}
					}]
				},
				fields: {
					id: {
						key: true,
						create: false,
						edit: false,
						list: false
						},
					quoteid: {
						list: false,
						type: 'hidden',
						defaultValue: quoteid
						},
					sectiontag: {
						title: 'Tag',
						inputClass: 'validate[ajax[validateSectionTag]]',
						width :'5%'
						},
					side: {
						title: 'Side',
						options: [{ Value: '1', DisplayText: 'Front' }, { Value: '2', DisplayText: 'Left' }, { Value: '3', DisplayText: 'Right' }, { Value: '4', DisplayText: 'Rear' }],
						list: true,
						width :'7%',
					},
					plength: {
						title: 'Length',
						inputClass: 'validate[required,custom[number]]',
						width :'7%',
						sorting: false,
						},
					pheight: {
						title: 'Height',
						inputClass: 'validate[required,custom[number]]',
						width :'6%',
						sorting: false
						},
					support: {
						title: 'Support',
						options: [{ Value: '1', DisplayText: 'Posts' }, { Value: '2', DisplayText: 'Brackets' }, { Value: '0', DisplayText: 'None' }],
						list: true,
						width :'6%',
						sorting: false
					},
					postposition: {
						title: 'PostPos.',
						options: [{ Value: '1', DisplayText: 'Left' }, { Value: '2', DisplayText: 'Right' }, { Value: '3', DisplayText: 'Both' }, { Value: '4', DisplayText: 'None' }],
						list: true,
						width :'7%',
						sorting: false
					},
					postmaterial: {
						title: 'PostMat.',
						options: [{ Value: 'none', DisplayText: 'none' }, { Value: '51R-1.6', DisplayText: '51R-1.6' }, { Value: '51S-1.6', DisplayText: '51S-1.6' }, { Value: '65S-1.6', DisplayText: '65S-1.6' },{ Value: '76R-2.0', DisplayText: '76R-2.0' },{ Value: '76S-3.0', DisplayText: '76S-3.0' },{ Value: '100S-3.0', DisplayText: '100S-3.0' },],
						list: true,
						width :'7%',
						sorting: false
					},
					postbaseplate: {
						title: 'Baseplate',
						options: [{ Value: '1', DisplayText: 'Type1' }, { Value: '2', DisplayText: 'Type2' }, { Value: '3', DisplayText: 'Type3' }, { Value: '4', DisplayText: 'Type4' }],
						list: true,
						width :'7%',
						sorting: false
					},
					brackets: {
						title: 'Brkts',
						width :'5%',
						sorting: false
						},
					brackettype: {
						title: 'BrktType',
						options: bdata,
						list: true,
						sorting: false,
						width :'7%'
					},
					bracketspacing: {
						title: 'BrktSpc',
						width :'5%',
						sorting: false
						},
					sectiontype: {
						title: 'Section',
						options: [{ Value: '1', DisplayText: 'Filled' }, { Value: '0', DisplayText: 'Wall' }, { Value: '0', DisplayText: 'Open' }],
						list: true,
						width :'6%',
						sorting: false
					},
					filltype: {
						title: 'Fill Type',
						dependsOn: 'sectiontype',
						options: function (data) {
							if (data.source == 'list') {
								//Return url of all countries for optimization. 
								//This method is called for each row on the table and jTable caches options based on this url.
							return 'ajaxfunctions.php?quoteaction=getFillTypeOptions&sectiontype=' + data.dependedValues.sectiontype;
							}
	 
							//This code runs when user opens edit/create form or changes continental combobox on an edit/create form.
							//data.source == 'edit' || data.source == 'create'
							return 'ajaxfunctions.php?quoteaction=getFillTypeOptions&sectiontype=' + data.dependedValues.sectiontype;
						},
						list: true,
						width :'8%'
					},
					fabrictype: {
						title: 'Fabric Type',
						dependsOn: 'filltype', //Countries depends on continentals. Thus, jTable builds cascade dropdowns!
						options: function (data) {
							if (data.source == 'list') {
								//Return url of all countries for optimization. 
								//This method is called for each row on the table and jTable caches options based on this url.
							return 'ajaxfunctions.php?quoteaction=getFabricTypeOptions&filltype=' + data.dependedValues.filltype;
							}
	 
							//This code runs when user opens edit/create form or changes continental combobox on an edit/create form.
							//data.source == 'edit' || data.source == 'create'
							return 'ajaxfunctions.php?quoteaction=getFabricTypeOptions&filltype=' + data.dependedValues.filltype;
						},
						list: true,
						sorting: false,
						width :'10%'
					},
					rollerblinds: {
						title: 'Rollr',
						type: 'checkbox',
						values: { 'false': 'No', 'true': 'Yes' },
						defaultValue: 'false',
						sorting: false,
						width :'4%',
					},
					rollerfabric: {
						title: 'R.Fabric',
						width :'13%',
						options: [{ Value: '0', DisplayText: 'None' },{ Value: '1', DisplayText: '503 - White' }, { Value: '2', DisplayText: '503 - Black' }, { Value: '3', DisplayText: 'Clear' }],
						list: true,
						sorting: false
					},
				},
				//Initialize validation logic when a form is created
				formCreated: function (event, data) {
					data.form.validationEngine();
				},
				//Validate form when it is being submitted
				formSubmitting: function (event, data) {
				//	return data.form.validationEngine('validate');
				},
				//Dispose validation logic when form is closed
				formClosed: function (event, data) {
					data.form.validationEngine('hide');
					data.form.validationEngine('detach');
				},

				 
				recordsLoaded: function (event, data) {
					var totallength = 0;
					var totalheight = 0;
					
					$.each(data.records, function(index, record) {
						totallength += Number(record.plength);
						if (totalheight < Number(record.pheight))
						totalheight = Number(record.pheight);
					});
					
					$("#totallength").html(Math.round(totallength,2));
					$("#totalheight").html(Math.round(totalheight,2));
					
				},
				
				
				//Register to selectionChanged event to hanlde events
				selectionChanged: function () {
					//Get all selected rows
					var $selectedRows = $('#FrontTableContainer').jtable('selectedRows');
	 
					$('#table_message').empty();
					if ($selectedRows.length > 0) {
						//Show selected rows
						$selectedRows.each(function () {
							var record = $(this).data('record');
							$('#table_message').html('<div style="padding:5px">'+$selectedRows.length+' rows selected!</div>');
						});
					} else {
						//No rows selected
						$('#table_message').html('<div style="padding:5px">No rows selected!</div>');
					}
				},
				recordUpdated: function (event, data) {
					$('#FrontTableContainer').jtable('reload');				
				},
			});

			//Re-load records when user click 'Populate' button.
			$('#UpdateButton').click(function (e) {
				e.preventDefault();
				

							//perform your custom job...
							//Get all selected rows
							var $selectedRows = $('#FrontTableContainer').jtable('selectedRows');

							if ($selectedRows.length > 0) {
								//Show selected rows
								$selectedRows.each(function () {
									var record = $(this).data('record');
									var jsobObj = {
													record: {
														id: record.id,
														quoteid: record.quoteid,
														sectiontag: record.sectiontag,
														side: record.side,
														plength: record.plength,
														pheight: record.pheight,
														support: record.support,
														postposition: record.postposition,
														postmaterial: record.postmaterial,
														postbaseplate: record.postbaseplate,
														brackets: record.brackets,
														brackettype: record.brackettype,
														bracketspacing: record.bracketspacing,
														sectiontype: record.sectiontype,
														filltype: record.filltype,
														fabrictype: record.fabrictype,
														rollerblinds: record.rollerblinds,
														rollerfabric: record.rollerfabric,
													}
									}
									if ($("#plength1").val())
									jsobObj.record.plength = $("#plength1").val()

									if ($("#pheight1").val())
									jsobObj.record.pheight = $("#pheight1").val()

									if ($("#supporttype1").select2("val"))
									jsobObj.record.support = $("#supporttype1").select2("val");

									if ($("#postposition1").select2("val"))
									jsobObj.record.postposition = $("#postposition1").select2("val");

									if ($("#brackets1").val())
									jsobObj.record.brackets = $("#brackets1").val();
									
									if ($("#brackettype1").select2("val"))
									jsobObj.record.brackettype = $("#brackettype1").select2("val");

									if ($("#bracketspacing1").val())
									jsobObj.record.bracketspacing = $("#bracketspacing1").val();
									
									if ($("#sectiontype1").select2("val"))
									jsobObj.record.sectiontype = $("#sectiontype1").select2("val");
									
									if ($("#filltype1").select2("val"))
									jsobObj.record.filltype = $("#filltype1").select2("val");;
									
									if ($("#fabrictype1").select2("val"))
									jsobObj.record.fabrictype = $("#fabrictype1").select2("val");

									

									//Copy Selected Records one by one
									$('#FrontTableContainer').jtable('updateRecord', jsobObj);
								});
							} else {
								//No rows selected
								$('#table_message').html('<div style="padding:5px">No rows selected!</div>');
							}
										
			});
			
			$('#UseRecommendedButton').click(function (e) {
				
				canopytype = $("input[name='canopytype']:checked").val();
				freestanding = $("input[name='freestanding']:checked").val();
				openingtype = $("input[name='openingtype']:checked").val();

				var msg="";
				
				if((openingtype)==0)
				{
					if($('#openingtypeselect').select2('val') == "")
					msg += "Please Select Opening Type";
					if($('#openingtypeselectfabric').select2('val') == "")
					msg += " | Please Select Opening Type Fabric";
				} else
				if((openingtype)==1)
				{
					if($('#openingtypeleft').select2('val') == "")
					msg += " | Please Select Left Opening Type";
					if($('#openingtypeleftfabric').select2('val') == "")
					msg += " | Please Select Left Opening Fabric";
					if($('#openingtypefront').select2('val') == "")
					msg += " | Please Select Front Opening Type";
					if($('#openingtypefrontfabric').select2('val') == "")
					msg += " | Please Select Front Opening Fabric";
					if($('#openingtyperight').select2('val') == "")
					msg += " | Please Select Right Opening Type";
					if($('#openingtyperightfabric').select2('val') == "")
					msg += " | Please Select Right Opening Fabric";
					if($('#openingtyperear').select2('val') == "")
					msg += " | Please Select Rear Opening Type";
					if($('#openingtyperearfabric').select2('val') == "")
					msg += " | Please Select Rear Opening Fabric";
				}
				
				
				
							
				if($('#frontrailmaterial').select2('val') == "")
				msg = " | Please Select Front Rail Material";
				if($('#backrailmaterial').select2('val') == "")
				msg = " | Please Select Back Rail Material";
				
				//Validation for autopopulate
				if(msg)
				{
					$('#MessageContainer').html(msg);
				}
				else
				{
						$('#MessageContainer').empty();
						$.ajax({  
								type: 'POST',       
								url: 'ajaxfunctions.php',
								data: 	"canopytype="+ canopytype+
										"&freestanding="+ freestanding+
										"&quoteid="+ $('#quoteid').val()+
										"&frontrailmaterial="+ $('#frontrailmaterial').select2('data').text+
										"&leftsuffitwidth="+ $('#leftsuffitwidth').val()+
										"&rightsuffitwidth="+ $('#rightsuffitwidth').val()+
										"&openingtype="+ $("input[name='openingtype']:checked").val()+
										"&openingtypeleft="+ $('#openingtypeleft').select2('val')+
										"&openingtypefront="+ $('#openingtypefront').select2('val')+
										"&openingtyperight="+ $('#openingtyperight').select2('val')+
										"&openingtyperear="+ $('#openingtyperear').select2('val')+
										"&openingtypeselect="+ $('#openingtypeselect').select2('val')+
										"&openingtypeleftfabric="+ $('#openingtypeleftfabric').select2('val')+
										"&openingtypefrontfabric="+ $('#openingtypefrontfabric').select2('val')+
										"&openingtyperightfabric="+ $('#openingtyperightfabric').select2('val')+
										"&openingtyperearfabric="+ $('#openingtyperearfabric').select2('val')+
										"&openingtypeselectfabric="+ $('#openingtypeselectfabric').select2('val')+
										"&quoteaction=autopopulatestep1",  
								dataType: 'json',
								cache: false,                           
								success: function(data)
								{  
									autopopulatestep1(data);
								}
						});
				} 
	
				
			});
			
			$('#UseRecommendedButtonStep2').click(function (e) {
				
				for (var rowid = 0; rowid < noofsteps; rowid++) {
					recarchqty = $('#recarchqty'+rowid).text();
					$('#archqty'+rowid).val(recarchqty);
					
					recarchspacing = $('#recarchspacing'+rowid).text();
					$('#archspacing'+rowid).val(recarchspacing);
					
					recarchbraces = $('#recarchbraces'+rowid).text();
					if(recarchbraces == 'Yes')
					{
						$("#archbraces"+rowid).select2("val", 1);
						$('#usearchbraces'+rowid).val('1');
					}
					
					else
					{
						$("#archbraces"+rowid).select2("val", 0);
						$('#usearchbraces'+rowid).val('0');
					}		
					
					reccentrebraceqty = $('#reccentrebraceqty'+rowid).text();
					$('#centrebraceqty'+rowid).val(reccentrebraceqty);
				}

			});
			
			$('#UseRecommendedButtonStep3').click(function (e) {
			  	$.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=lookupsteparray', // JQuery loads serverside.php
				  data: 'quoteid=' + quoteid,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  { populateEndFillTable(data); }
							
				});
			});
			
			//Load person list from server
			$('#FrontTableContainer').jtable('load');
			// Adding an additional row at the end of the table for showing sum results
			$('#FrontTableContainer table.jtable').append('<tfoot><tr><td class="cell" colspan="3">Overall Dimension :</td><td id="totallength"  class="sumcell"></td><td id="totalheight"  class="sumcell"></td></tr></tfoot>');
			// Adding a div into the footer panel of jTable with custom message
			$("#table_message").appendTo("#FrontTableContainer .jtable-bottom-panel .jtable-left-area").addClass("filter_results");


/*------------Populating tables and fields for Each Step from database ------------------*/

	// On Selecting Canopy Fabric Type, populate Fill Type
		$("#canopyfabrictype").on("select2-selecting", function(e) { 
		
		quoteid = $('#quoteid').val();
		$('#usecanopyfabrictype').val(e.val);
		
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=servervalidatestep1', // JQuery loads serverside.php
				  data: 'quoteid='+quoteid+'&lookupfabrictype=' + e.val,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					for (var rowid = 0; rowid < noofsteps; rowid++) {
						$('#recarchqty'+rowid).text(data[rowid]['recarchqty']);
						$('#recarchspacing'+rowid).text(data[rowid]['recarchspacing']);
					}
						
				  }
			  });
		});
		
			//Load data for Step 1 from Database
		function autopopulatefieldsstep1(data) {
			$('#nooffrontposts').val(data.nooffrontposts);	
			$('#leftsuffitwidth').val(data.leftsuffitwidth);	
			$('#rightsuffitwidth').val(data.rightsuffitwidth);
			if(data.archmaterial)
			{
				$('#archmaterial').select2('val',data.archmaterial);	
				$('#frontrailmaterial').select2('val',data.frontrailmaterial);	
				$('#backrailmaterial').select2('val',data.backrailmaterial);
			}
		}
			
			
		//Auto populating Front Array Fields in Step 2
		function autopopulatefieldsstep2(data) {
			
			if(isnoofstepschanged == 1)
			{ 
			
			//Initialize Select2 dropdown
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=getcanopyfabrictypeSelect2', // JQuery loads serverside.php
				  data: '',
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					// Fill Type Drop Down
						$("#canopyfabrictype").select2({
							placeholder: "Select Fabric",
							width:'200px',
							data:data
						});
					  $("#canopyfabrictype").select2("val",1);
					  $('#usecanopyfabrictype').val('1');

				  }
			  });
			 
				
			//Initially remove all the existing rows
			$("#archtable").find("th").remove();
			$("#archtable").find("tr").remove();
			
			noofsteps = data['noofsteps'];
			$('#noofsteps').val(noofsteps); 

				// Create rows and populate values from data
				for (var newrowid = 0; newrowid < data['noofsteps']; newrowid++) {
				  
					var newRow = $("<tr>");
					var cols = "";
					cols += '<th style="text-align:left">Step ' + (newrowid+1) +'&nbsp;&nbsp;&nbsp;<font size="2">(length='+data[newrowid]["length"]+', width='+data[newrowid]["width"]+')</font></th>';
					cols += '<th>Input</th>';
					cols += '<th>Recommended Value</th>';
					newRow.append(cols);
					$("#archtable").append(newRow);
					
					var newRow = $("<tr>");
					var cols = "";
					cols += '<td><label for="archqty' + newrowid + '">Arch Quantity</label></td>';
					cols += '<td><input class="pure-field" name="archqty' + newrowid + '" id="archqty' + newrowid + '" style="padding-left:10px" /></td>';
					cols += '<td><div id="recarchqty' + newrowid + '">'+data[newrowid]['recarchqty']+'</div></td>';
					newRow.append(cols);
					$("#archtable").append(newRow);
	
					var newRow = $("<tr>");
					var cols = "";
					cols += '<td><label for="archspacing' + newrowid + '">Arch Spacing</label></td>';
					cols += '<td><input class="pure-field" name="archspacing' + newrowid + '" id="archspacing' + newrowid + '" style="padding-left:10px" /></td>';
					cols += '<td><div id="recarchspacing' + newrowid + '">'+data[newrowid]['recarchspacing']+'</div></td>';
					newRow.append(cols);
					$("#archtable").append(newRow);
	
					var newRow = $("<tr>");
					var cols = "";
					cols += '<td><label for="archbraces' + newrowid + '">Arch Braces</label></td>';
					cols += '<td><div style="" id="archbraces' + newrowid + '"></div><input type="hidden" id="usearchbraces' + newrowid + '" name="usearchbraces' + newrowid + '" value=""></td>';
					cols += '<td><div id="recarchbraces' + newrowid + '">'+data[newrowid]['recarchbraces']+'</div></td>';
					newRow.append(cols);
					$("#archtable").append(newRow);
					
					var newRow = $("<tr>");
					var cols = "";
					cols += '<td><label for="centrebraceqty' + newrowid + '">Centre Brace Quantity</label></td>';
					cols += '<td><input class="pure-field" name="centrebraceqty' + newrowid + '" id="centrebraceqty' + newrowid + '" style="padding-left:10px" /></td>';
					cols += '<td><div id="reccentrebraceqty' + newrowid + '">'+data[newrowid]['reccentrebraceqty']+'</div></td>';
					newRow.append(cols);
					$("#archtable").append(newRow);
					
					//Initialize Select2 dropdown
						//use archbraces dropdown
						$("#archbraces"+newrowid).select2({
									placeholder: "Select",
									width:'158px',
									allowClear: true,
									data:[{id:0,text:'No'},{id:1,text:'Yes'}]
						});
						
						// On Selecting Arch braces, insert value into input hidden field
						$("#archbraces"+newrowid).on("select2-selecting", function(e) { 
							hiddenfieldid = "use"+$(this).attr('id');
							$("#"+hiddenfieldid).val(e.val);
							
						});
				}
			isnoofstepschanged = 0;
			}
			
		}


			//Auto populating Front Array Table in Step 1
			function autopopulatestep1(data) {		

				var sectionno=0;
				
				$('#nooffrontposts').val(data.nofrontpost);
				postmaterial = data.postmaterial;
				postbaseplate = 1;
				
				
				// LEFT ARRAY
				for ( var j=0; j<data["noofsteps"]; j++) // STEPS
				{
					for ( var k=0; k<data[j]["noofleftsections"]; k++) // SECTIONS
					{
						// if its the first step, set the sides "Left" 
						if(j==0)
						{
							// if k = last section or there is only one section, postposition = both
							if((k==data[j]["noofleftsections"]-1) || (data[j]["noofleftsections"]==1))
							postposition = 4;
							else
							postposition = 2;
							
							$('#FrontTableContainer').jtable('addRecord', {
								record: {
											sectiontag: String.fromCharCode(65 + sectionno),
											side: "2",
											quoteid: data["quoteid"],
											plength: data[j]["lplength"],
											pheight: data[j]["lpheight"],
											support: "1",
											postposition: postposition,
											postmaterial:postmaterial,
											postbaseplate:postbaseplate,
											brackets: "0",
											brackettype: "0",
											bracketspacing: "0",
											sectiontype: "1",
											filltype: data["filltypeleft"],
											fabrictype: data["fabrictypeleft"],
											rollerblinds: false,
											rollerfabric: "0",
								}
							});
							sectionno++; // After creating section, increment section number
						}
						
					}
						
				}

				
				// FRONT ARRAY
				for ( var i=0; i<data["nooffrontsections"]; i++)
				{
					// if i = last section or there is only one section, postposition = both
					if((i==data["nooffrontsections"]-1) || (data["nooffrontsections"]==1))
					postposition = 3;
					else
					postposition = 1;
					
		
					$('#FrontTableContainer').jtable('addRecord', {
						record: {
									sectiontag: String.fromCharCode(65 + sectionno),
									side: "1",
									quoteid: data["quoteid"],
									plength: data["fplength"],
									pheight: data["fpheight"],
									support: "1",
									postposition: postposition,
									postmaterial:postmaterial,
									postbaseplate:postbaseplate,
									brackets: "0",
									brackettype: "0",
									bracketspacing: "0",
									sectiontype: "1",
									filltype: data["filltypefront"],
									fabrictype: data["fabrictypefront"],
									rollerblinds: false,
									rollerfabric: "0",
						}
					});	
					sectionno++; // After creating section, increment section number
					
				}
				
				// RIGHT ARRAY
				for ( var j=0; j<data["noofsteps"]; j++) // STEPS
				{
					for ( var k=0; k<data[j]["noofrightsections"]; k++) // SECTIONS
					{
						// if its the last step, set the sides "Right"
						if(j==(data["noofsteps"]-1))
						{
							// if k = last section or there is only one section, postposition = both
							if((k==data[j]["noofrightsections"]-1) || (data[j]["noofrightsections"]==1))
							postposition = 4;
							else
							postposition = 2;
							
							$('#FrontTableContainer').jtable('addRecord', {
								record: {
											sectiontag: String.fromCharCode(65 + sectionno),
											side: "3",
											quoteid: data["quoteid"],
											plength: data[j]["rplength"],
											pheight: data[j]["rpheight"],
											support: "1",
											postposition: postposition,
											postmaterial:postmaterial,
											postbaseplate:postbaseplate,
											brackets: "0",
											brackettype: "0",
											bracketspacing: "0",
											sectiontype: "1",
											filltype: data["filltyperight"],
											fabrictype: data["fabrictyperight"],
											rollerblinds: false,
											rollerfabric: "0",
								}
							});
							sectionno++; // After creating section, increment section number
						}
					
					}
						
				}	
				
				// REAR ARRAY
				for ( var j=data["noofsteps"]-1; j>=0; j--) // STEPS
				{
					$('#FrontTableContainer').jtable('addRecord', {
						record: {
									sectiontag: String.fromCharCode(65 + sectionno),
									side: "4",
									quoteid: data["quoteid"],
									plength: data[j]["length"],
									pheight: "2400",
									support: "2",
									postposition: "4",
									postmaterial:postmaterial,
									postbaseplate:postbaseplate,
									brackets: data[j]["nobrackets"],
									brackettype: "1",
									bracketspacing: data[j]["bracket_spacing"],
									sectiontype: "1",
									filltype: data["filltyperear"],
									fabrictype: data["fabrictyperear"],
									rollerblinds: false,
									rollerfabric: "0",
						}
					});
					sectionno++; // After creating section, increment section number
				}	
							
			}

			function onloadstep0() {
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=getFillTypeOptionsSelect2', // JQuery loads serverside.php
				  data: 'quoteid=' + quoteid,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(filltypedata) // Variable data contains the data we get from serverside
				  {
						 $("#openingtypeselect").select2({
							 placeholder: "Select",
							 width:'120px',
							 allowClear: true,
							 data: filltypedata	 
						 });
						 $("#openingtypeleft").select2({
							 placeholder: "Select",
							 width:'120px',
							 allowClear: true,
							 data: filltypedata	 
						 });
						 $("#openingtypefront").select2({
							 placeholder: "Select",
							 width:'120px',
							 allowClear: true,
							 data: filltypedata	 
						 });
						 $("#openingtyperight").select2({
							 placeholder: "Select",
							 width:'120px',
							 allowClear: true,
							 data: filltypedata	 
						 });
						 $("#openingtyperear").select2({
							 placeholder: "Select",
							 width:'120px',
							 allowClear: true,
							 data: filltypedata	 
						 });
						 
				  }
			  });
				
			}
			
			$("input[name='openingtype']").change(function() {

				$("#rowopeningtypeleft").fadeToggle("slow");
				$("#rowopeningtypefront").fadeToggle("slow");
				$("#rowopeningtyperight").fadeToggle("slow");
				$("#rowopeningtyperear").fadeToggle("slow");			
				$("#rowopeningtypeselect").fadeToggle("slow");
				
	        });
			
	// Preinitialize dependent dropdowns
			 $("#openingtypeleftfabric").select2({
				 placeholder: "Fill",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
	// Preinitialize dependent dropdowns
			 $("#openingtypefrontfabric").select2({
				 placeholder: "Select",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
	// Preinitialize dependent dropdowns
			 $("#openingtyperightfabric").select2({
				 placeholder: "Select",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
	// Preinitialize dependent dropdowns
			 $("#openingtyperearfabric").select2({
				 placeholder: "Select",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
	// Preinitialize dependent dropdowns
			 $("#openingtypeselectfabric").select2({
				 placeholder: "Select",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });

			
		//On Selecting Fill Type, populate Fabric Type
			$("#openingtypeleft").on("select2-selecting", function(ft) { 
			
				  $.ajax({ // ajax call starts
					  url: 'ajaxfunctions.php?quoteaction=getFabricTypeOptionsSelect2', // JQuery loads serverside.php
					  data: 'searchid=' + ft.val,
					  dataType: 'json', // Choosing a JSON datatype
					  success: function(data) // Variable data contains the data we get from serverside
					  {
						// Fabric Type Drop Down
							$("#openingtypeleftfabric").select2({
								placeholder: "Fabric",
								width:'120px',
								allowClear: true,
								data:data
							});
					  }
				  });
			//console.log ("selecting val="+ ft.val+" ft="+ JSON.stringify(ft.object.text));
			})
			
		//On Selecting Fill Type, populate Fabric Type
			$("#openingtypefront").on("select2-selecting", function(ft) { 
			
				  $.ajax({ // ajax call starts
					  url: 'ajaxfunctions.php?quoteaction=getFabricTypeOptionsSelect2', // JQuery loads serverside.php
					  data: 'searchid=' + ft.val,
					  dataType: 'json', // Choosing a JSON datatype
					  success: function(data) // Variable data contains the data we get from serverside
					  {
						// Fabric Type Drop Down
							$("#openingtypefrontfabric").select2({
								placeholder: "Fabric",
								width:'120px',
								allowClear: true,
								data:data
							});
					  }
				  });
			//console.log ("selecting val="+ ft.val+" ft="+ JSON.stringify(ft.object.text));
			})
			
		//On Selecting Fill Type, populate Fabric Type
			$("#openingtyperight").on("select2-selecting", function(ft) { 
			
				  $.ajax({ // ajax call starts
					  url: 'ajaxfunctions.php?quoteaction=getFabricTypeOptionsSelect2', // JQuery loads serverside.php
					  data: 'searchid=' + ft.val,
					  dataType: 'json', // Choosing a JSON datatype
					  success: function(data) // Variable data contains the data we get from serverside
					  {
						// Fabric Type Drop Down
							$("#openingtyperightfabric").select2({
								placeholder: "Fabric",
								width:'120px',
								allowClear: true,
								data:data
							});
					  }
				  });
			//console.log ("selecting val="+ ft.val+" ft="+ JSON.stringify(ft.object.text));
			})

		//On Selecting Fill Type, populate Fabric Type
			$("#openingtyperear").on("select2-selecting", function(ft) { 
			
				  $.ajax({ // ajax call starts
					  url: 'ajaxfunctions.php?quoteaction=getFabricTypeOptionsSelect2', // JQuery loads serverside.php
					  data: 'searchid=' + ft.val,
					  dataType: 'json', // Choosing a JSON datatype
					  success: function(data) // Variable data contains the data we get from serverside
					  {
						// Fabric Type Drop Down
							$("#openingtyperearfabric").select2({
								placeholder: "Fabric",
								width:'120px',
								allowClear: true,
								data:data
							});
					  }
				  });
			//console.log ("selecting val="+ ft.val+" ft="+ JSON.stringify(ft.object.text));
			})

		//On Selecting Fill Type, populate Fabric Type
			$("#openingtypeselect").on("select2-selecting", function(ft) { 
			
				  $.ajax({ // ajax call starts
					  url: 'ajaxfunctions.php?quoteaction=getFabricTypeOptionsSelect2', // JQuery loads serverside.php
					  data: 'searchid=' + ft.val,
					  dataType: 'json', // Choosing a JSON datatype
					  success: function(data) // Variable data contains the data we get from serverside
					  {
						// Fabric Type Drop Down
							$("#openingtypeselectfabric").select2({
								placeholder: "Fabric",
								width:'120px',
								allowClear: true,
								data:data
							});
					  }
				  });
			//console.log ("selecting val="+ ft.val+" ft="+ JSON.stringify(ft.object.text));
			})

			
			function onloadstep2(data) {
				
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=retrieveonloadstep2', // JQuery loads serverside.php
				  data: 'quoteid=' + quoteid,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					for (i=0;i<noofsteps;i++)
					{
						$('#archqty'+i).val(data[i]['archqty']);
						$('#archspacing'+i).val(data[i]['archspacing']);
						$('#archbraces'+i).select2('val',(data[i]['archbraces']));
						$('#usearchbraces'+i).val(data[i]['archbraces']);
						$('#centrebraceqty'+i).val(data[i]['centrebraceqty']);

					}
						$('#canopyfabrictype').select2('val',(data['canopyfabrictype']));
						$('#usecanopyfabrictype').val(data['canopyfabrictype']);
				  }
			  });
				
			}
			
			function onloadstep3(data) {
			
			//Initially remove all the existing rows
			$("#EndFillsTableContainer").find("th").remove();
			$("#EndFillsTableContainer").find("tr").remove();
	
				
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=lookupsteparray', // JQuery loads serverside.php
				  data: 'quoteid=' + quoteid,
				  async: true,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					noofsteps = data['noofsteps'];
					$('#noofsteps').val(noofsteps);
					 
					// Create rows for Endfill Table
					for (var newrowid = 0; newrowid < data['noofsteps']; newrowid++) 
					{
						//Heading
						var newRow = $("<tr>");
						var cols = "";
						cols += '<th style="text-align:left">Step ' + (newrowid+1) +'&nbsp;&nbsp;&nbsp;<font size="2">(length='+data[newrowid]["length"]+', width='+data[newrowid]["width"]+')</font></th>';
						cols += '<th>EndFill Required</th>';
						cols += '<th>Length</th>';
						cols += '<th>Height</th>';
						cols += '<th>Fabric</th>';
						cols += '<th>Type</th>';
						cols += '<th>Stub</th>';
						newRow.append(cols);
						$("#EndFillsTableContainer").append(newRow);
						
						//Left
						var newRow = $("<tr>");
						var cols = "";
						cols += '<td><input class="pure-field-noborder" name="endfillpartleft' + newrowid + '" id="endfillpartleft' + newrowid + '" style="padding-left:10px" value="Left" readonly/></td>';
						cols += '<td><input type="checkbox" name="endfillrequiredleft' + newrowid + '" id="endfillrequiredleft' + newrowid + '" style="padding-left:10px" /></td>';
						cols += '<td><input class="pure-field" name="endfilllengthleft' + newrowid + '" id="endfilllengthleft' + newrowid + '" style="padding-left:10px; width:100px" /></td>';
						cols += '<td><input class="pure-field" name="endfillheightleft' + newrowid + '" id="endfillheightleft' + newrowid + '" style="padding-left:10px; width:100px" /></td>';
						cols += '<td><input type="hidden" name="endfillfabricleft' + newrowid + '" id="endfillfabricleft' + newrowid + '" /></td>';
						cols += '<td><input type="hidden" name="endfilltypeleft' + newrowid + '" id="endfilltypeleft' + newrowid + '" /></td>';
						cols += '<td><input type="checkbox" name="endfillstubleft' + newrowid + '" id="endfillstubleft' + newrowid + '" style="padding-left:10px" /></td>';
						newRow.append(cols);
						$("#EndFillsTableContainer").append(newRow);
						
						//Middle
						var newRow = $("<tr>");
						var cols = "";
						cols += '<td><input class="pure-field-noborder" name="endfillpartmiddle' + newrowid + '" id="endfillpartmiddle' + newrowid + '" style="padding-left:10px" value="Middle" readonly/></td>';
						cols += '<td><input type="checkbox" name="endfillrequiredmiddle' + newrowid + '" id="endfillrequiredmiddle' + newrowid + '" style="padding-left:10px" /></td>';
						cols += '<td><input class="pure-field" name="endfilllengthmiddle' + newrowid + '" id="endfilllengthmiddle' + newrowid + '" style="padding-left:10px; width:100px" /></td>';
						cols += '<td><input class="pure-field" name="endfillheightmiddle' + newrowid + '" id="endfillheightmiddle' + newrowid + '" style="padding-left:10px; width:100px" /></td>';
						cols += '<td><input type="hidden" name="endfillfabricmiddle' + newrowid + '" id="endfillfabricmiddle' + newrowid + '" /></td>';
						cols += '<td><input type="hidden" name="endfilltypemiddle' + newrowid + '" id="endfilltypemiddle' + newrowid + '" /></td>';
						cols += '<td><input type="checkbox" name="endfillstubmiddle' + newrowid + '" id="endfillstubmiddle' + newrowid + '" style="padding-left:10px" /></td>';
						newRow.append(cols);
						$("#EndFillsTableContainer").append(newRow);
						
						//Right
						var newRow = $("<tr>");
						var cols = "";
						cols += '<td><input class="pure-field-noborder" name="endfillpartright' + newrowid + '" id="endfillpartright' + newrowid + '" style="padding-left:10px" value="Right" readonly/></td>';
						cols += '<td><input type="checkbox" name="endfillrequiredright' + newrowid + '" id="endfillrequiredright' + newrowid + '" style="padding-left:10px" /></td>';
						cols += '<td><input class="pure-field" name="endfilllengthright' + newrowid + '" id="endfilllengthright' + newrowid + '" style="padding-left:10px; width:100px" /></td>';
						cols += '<td><input class="pure-field" name="endfillheightright' + newrowid + '" id="endfillheightright' + newrowid + '" style="padding-left:10px;  width:100px" /></td>';
						cols += '<td><input type="hidden" name="endfillfabricright' + newrowid + '" id="endfillfabricright' + newrowid + '" /></td>';
						cols += '<td><input type="hidden" name="endfilltyperight' + newrowid + '" id="endfilltyperight' + newrowid + '" /></td>';
						cols += '<td><input type="checkbox" name="endfillstubright' + newrowid + '" id="endfillstubright' + newrowid + '" style="padding-left:10px" /></td>';
						newRow.append(cols);
						$("#EndFillsTableContainer").append(newRow);
						
						//Initialize dropdowns
						$('#endfillfabricleft' + newrowid).select2({
							data:[{id:'Clear',text:'Clear'},{id:$('#canopyfabrictype').select2('data').text,text:$('#canopyfabrictype').select2('data').text},{id:'None',text:'None'}],
							width:'100'
						});
						$('#endfilltypeleft' + newrowid).select2({
							data:[{id:'Open',text:'Open'},{id:'L-shaped',text:'L-shaped'}],
							width:'100'
						});
						
						$('#endfillfabricmiddle' + newrowid).select2({
							data:[{id:'Clear',text:'Clear'},{id:$('#canopyfabrictype').select2('data').text,text:$('#canopyfabrictype').select2('data').text},{id:'None',text:'None'}],
							width:'100'
						});
						$('#endfilltypemiddle' + newrowid).select2({
							data:[{id:'Open',text:'Open'},{id:'L-shaped',text:'L-shaped'}],
							width:'100'
						});

						$('#endfillfabricright' + newrowid).select2({
							data:[{id:'Clear',text:'Clear'},{id:$('#canopyfabrictype').select2('data').text,text:$('#canopyfabrictype').select2('data').text},{id:'None',text:'None'}],
							width:'100'
						});
						$('#endfilltyperight' + newrowid).select2({
							data:[{id:'Open',text:'Open'},{id:'L-shaped',text:'L-shaped'}],
							width:'100'
						});
					}
					retrieveonloadstep3();

				  },
				  failure: function(data) {
					  alert('ajax failed');
				  }
				  
			  });
				
			
			}


	function retrieveonloadstep3(data) {
	  $.ajax({ // ajax call starts
		  url: 'ajaxfunctions.php?quoteaction=retrieveonloadstep3', // JQuery loads serverside.php
		  data: 'quoteid=' + quoteid,
		  dataType: 'json', // Choosing a JSON datatype
		  success: function(data) // Variable data contains the data we get from serverside
		  {
			  console.log(data);
			//Populate Values for Endfill Table from database
			for (var step = 0; step < noofsteps; step++) 
			{
				//Left Part
				if(data[step]['endfillrequiredleft'])
				$('#endfillrequiredleft'+step).prop('checked', true); //Endfill is required				
				$('#endfilllengthleft'+step).val(data[step]['endfilllengthleft']); //Endfill length = current width - previous
				$('#endfillheightleft'+step).val(data[step]['endfillheightleft']); //Endfill height = 12% of canopy height
				$('#endfillfabricleft'+step).select2('val',data[step]['endfillfabricleft']);
				$('#endfilltypeleft'+step).select2('val',data[step]['endfilltypeleft']);
				if(data[step]['endfillstubleft'])
				$('#endfillstubleft'+step).prop('checked', true);
				
				//Middle Part
				if(data[step]['endfillrequiredmiddle'])
				$('#endfillrequiredmiddle'+step).prop('checked', true); //Endfill is required
				$('#endfilllengthmiddle'+step).val(data[step]['endfilllengthmiddle']); //Endfill length = step length
				$('#endfillheightmiddle'+step).val(data[step]['endfillheightmiddle']); //Endfill height = 12% of canopy height
				$('#endfillfabricmiddle'+step).select2('val',data[step]['endfillfabricmiddle']);
				$('#endfilltypemiddle'+step).select2('val',data[step]['endfilltypemiddle']);
				if(data[step]['endfillstubmiddle'])
				$('#endfillstubmiddle'+step).prop('checked', true);

				//Right Part
				if(data[step]['endfillrequiredright'])
				$('#endfillrequiredright'+step).prop('checked', true); //Endfill is required
				$('#endfilllengthright'+step).val(data[step]['endfilllengthright']); //Endfill length = current width - next
				$('#endfillheightright'+step).val(data[step]['endfillheightright']); //Endfill height = 12% of canopy height
				$('#endfillfabricright'+step).select2('val',data[step]['endfillfabricright']);
				$('#endfilltyperight'+step).select2('val',data[step]['endfilltyperight']);
				if(data[step]['endfillstubright'])
				$('#endfillstubright'+step).prop('checked', true);
			}
		  }
	  });

	}
			
	function populateEndFillTable(data) {
		
		//Initially Clear Existing Values from the Table
		$("#EndFillsTableContainer").find('input[name^="endfillrequired"]').each(function () {
			$(this).closest('tr').find('input[name^="endfillrequired"]').prop('checked', false);
			$(this).closest('tr').find('input[name^="endfilllength"]').val('');
			$(this).closest('tr').find('input[name^="endfillheight"]').val('');
			$(this).closest('tr').find('input[name^="endfillfabric"]').select2('val',1);
			$(this).closest('tr').find('input[name^="endfilltype"]').select2('val',1);
			$(this).closest('tr').find('input[name^="endfillstub"]').prop('checked', false);
		});
		
		//Populate Values for Endfill Table
		for (var newrowid = 0; newrowid < data['noofsteps']; newrowid++) 
		{
			//Initialize previous and next step widths
			if(newrowid == 0)
				previousstepwidth = 0;
			else
				previousstepwidth = data[newrowid-1]["width"];
			
			
			if(newrowid == (data['noofsteps']-1))
				nextstepwidth = 0;
			else
				nextstepwidth = data[newrowid+1]["width"];

			//Left side of Endfill
			if(data[newrowid]["width"]>previousstepwidth)
			{
				$('#endfillrequiredleft'+newrowid).prop('checked', true); //Endfill is required
				$('#endfilllengthleft'+newrowid).val(data[newrowid]['width']-previousstepwidth); //Endfill length = current width - previous
				$('#endfillheightleft'+newrowid).val(0.12*data['canopywidth']); //Endfill height = 12% of canopy height
				$('#endfillfabricleft'+newrowid).select2('val',1);
				$('#endfilltypeleft'+newrowid).select2('val',1);
				$('#endfillstubleft'+newrowid).prop('checked', false);
				
				if($('#endfilllengthleft'+newrowid).val()>4000)
				$('#endfillstubleft'+newrowid).prop('checked', true); //stub is required
			}
			else
			{
				$('#endfillrequiredleft'+newrowid).prop('checked', false); //Endfill is NOT required
			}
			
			//Middle side of Endfill
			if(data[newrowid]["width"]<data['canopywidth'])
			{
				$('#endfillrequiredmiddle'+newrowid).prop('checked', true); //Endfill is required
				$('#endfilllengthmiddle'+newrowid).val(data[newrowid]['length']); //Endfill length = step length
				$('#endfillheightmiddle'+newrowid).val(0.12*data['canopywidth']); //Endfill height = 12% of canopy height
				$('#endfillfabricmiddle'+newrowid).select2('val',1);
				$('#endfilltypemiddle'+newrowid).select2('val',1);
				$('#endfillstubmiddle'+newrowid).prop('checked', false);
			}
			else
			{
				$('#endfillrequiredmiddle'+newrowid).prop('checked', false); //Endfill is NOT required	
			}
			
			//Right side of Endfill
			if(data[newrowid]["width"]>nextstepwidth)
			{
				$('#endfillrequiredright'+newrowid).prop('checked', true); //Endfill is required
				$('#endfilllengthright'+newrowid).val(data[newrowid]['width']-nextstepwidth); //Endfill length = current width - next
				$('#endfillheightright'+newrowid).val(0.12*data['canopywidth']); //Endfill height = 12% of canopy height
				$('#endfillfabricright'+newrowid).select2('val',1);
				$('#endfilltyperight'+newrowid).select2('val',1);
				$('#endfillstubright'+newrowid).prop('checked', false);
				
				if($('#endfilllengthright'+newrowid).val()>4000)
				$('#endfillstubright'+newrowid).prop('checked', true); //stub is required
			}
			else
			{
				$('#endfillrequiredright'+newrowid).prop('checked', false); //Endfill is NOT required
			}
		}
		
		
	}
	
	function onloadstep4(data) {
			
			//Initially remove all the existing rows
			$("#additionalcontainer").find("th").remove();
			$("#additionalcontainer").find("tr").remove();
	
				
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=lookupsteparray', // JQuery loads serverside.php
				  data: 'quoteid=' + quoteid,
				  async: true,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					noofsteps = data['noofsteps'];
					$('#noofsteps').val(noofsteps);
					
						//Heading
						var newRow = $("<tr>");
						var cols = "";
						cols += '<th style="text-align:left">Sides</th>';
						cols += '<th>Scolloped</th>';
						cols += '<th>Gutter</th>';
						cols += '<th>Downpipe</th>';
						cols += '<th>Timber</th>';
						newRow.append(cols);
						$("#additionalcontainer").append(newRow);
						
						//Front
						var newRow = $("<tr>");
						var cols = "";
						cols += '<td><input class="pure-field-noborder" name="sidefront" id="sidefront" style="padding-left:10px" value="Front" readonly/></td>';
						cols += '<td><input type="checkbox" name="scallopedfront" id="scallopedfront" style="padding-left:10px" /></td>';
						cols += '<td><input type="checkbox" name="gutterfront" id="gutterfront" style="padding-left:10px" /></td>';
						cols += '<td><input type="checkbox" name="downpipefront" id="downpipefront" style="padding-left:10px;" /></td>';
						cols += '<td><select name="timberfront" id="timberfront"></select></td>';
						newRow.append(cols);
						$("#additionalcontainer").append(newRow);
						
						//Initialize SELECT2 dropdown
						$("#timberfront").select2('destroy').html(newOptionsTT).select2({
								width:'100px',
								allowClear: true
						});
						
					// Create rows for Additional Table
					for (var newrowid = 0; newrowid < data['noofsteps']; newrowid++) 
					{
						
						//Backsteps
						var newRow = $("<tr>");
						var cols = "";
						cols += '<td><input class="pure-field-noborder" name="sidebackstep' + newrowid + '" id="sidebackstep' + newrowid + '" style="padding-left:10px" value="Back - ' + data[newrowid]["steptag"] + '" readonly/></td>';
						cols += '<td><input type="checkbox" name="scallopedbackstep' + newrowid + '" id="scallopedbackstep' + newrowid + '" style="padding-left:10px" /></td>';
						cols += '<td><input type="checkbox" name="gutterbackstep' + newrowid + '" id="gutterbackstep' + newrowid + '" style="padding-left:10px" /></td>';
						cols += '<td><input type="checkbox" name="downpipebackstep' + newrowid + '" id="downpipebackstep' + newrowid + '" style="padding-left:10px;" /></td>';
						cols += '<td><select name="timberbackstep' + newrowid + '" id="timberbackstep' + newrowid + '"></select></td>';
						newRow.append(cols);
						$("#additionalcontainer").append(newRow);
						
						//Initialize Brackettype SELECT2 dropdown
						$("#timberbackstep"+newrowid).select2('destroy').html(newOptionsTT).select2({
								width:'100px',
								allowClear: true
						});
						
					}
				
				retrieveonloadstep4();
				
				  },
				  failure: function(data) {
					  alert('ajax failed');
				  }
				  
			  });
				
			
			}
			
	function retrieveonloadstep4(data) {
	  $.ajax({ // ajax call starts
		  url: 'ajaxfunctions.php?quoteaction=retrieveonloadstep4', // JQuery loads serverside.php
		  data: 'quoteid=' + quoteid,
		  dataType: 'json', // Choosing a JSON datatype
		  success: function(data) // Variable data contains the data we get from serverside
		  {
			  console.log(data);
				if(data[0]['side'] == "Front")
				{
			//Populate Values for Additional config Table from database

					//FRONT
					if(data[0]['scolloped'])
					$('#scallopedfront').prop('checked', true);
					if(data[0]['gutter'])
					$('#gutterfront').prop('checked', true);
					if(data[0]['downpipe'])
					$('#downpipefront').prop('checked', true);
					if(data[0]['timber'])
					$('#timberfront').select2('val',data[0]['timber']);
				}  
			  
			for (var step = 0; step < noofsteps; step++) 
			{
				
					//BACK
					if(data[step+1]['scolloped'])
					$('#scallopedbackstep'+step).prop('checked', true);
					if(data[step+1]['gutter'])
					$('#gutterbackstep'+step).prop('checked', true);
					if(data[step+1]['downpipe'])
					$('#downpipebackstep'+step).prop('checked', true);
					if(data[step+1]['timber'])
					$('#timberbackstep'+step).select2('val',data[step+1]['timber']);
			}
		  }
	  });

	}
	
	
	
	function onloadstep5() {
		retrieveonloadstep5();
	}
			
	function retrieveonloadstep5() {
	  $.ajax({ // ajax call starts
		  url: 'ajaxfunctions.php?quoteaction=retrieveonloadstep5', // JQuery loads serverside.php
		  data: 'quoteid=' + quoteid,
		  dataType: 'json', // Choosing a JSON datatype
		  success: function(data) // Variable data contains the data we get from serverside
		  {
			 // console.log(data);

					if(data['adf']=='1')
					$('#adf_1').prop('checked', true);
					if(data['wq']=='1')
					$('#wq_1').prop('checked', true);
					if(data['fio']=='1')
					$('#fio_1').prop('checked', true);
					if(data['buildingconsent']=='1')
					$('#buildingconsent_1').prop('checked', true);
					if(data['gst']=='1')
					$('#gst_1').prop('checked', true);
					if(data['leanto']=='1')
					$('#leanto_1').prop('checked', true);
					if(data['hotdipgalv']=='1')
					$('#hotdipgalv_1').prop('checked', true);
					if(data['epoxy']=='1')
					$('#epoxy_1').prop('checked', true);
					if(data['polyester']=='1')
					$('#polyester_1').prop('checked', true);
					if(data['abcite']=='1')
					$('#abcite_1').prop('checked', true);
		  }
	  });

	}
			
	
			
/*------------Ending Populating tables and fields for Each Step from database ------------------*/


			
});
/*------------Ending Step 1 onReady Functions ------------------*/

/*------------Ending JQUERY onReady Functions ------------------*/


/*------------Common Functions ------------------*/



/****************Chained Select2 Dropdowns******************/
	function initializeDropDowns(newrowid) {		 
		
	//SupportType dropdown
		$("#supporttype"+newrowid).select2({
					placeholder: "Support",
					width:'100px',
					allowClear: true,
					data:[{id:1,text:'Posts'},{id:2,text:'Brackets'},{id:0,text:'None'}]
		});
		
		
	//PostPostion dropdown
		$("#postposition"+newrowid).select2({
					placeholder: "PostPos",
					width:'100px',
					allowClear: true,
					data:[{id:1,text:'Left'},{id:2,text:'Right'},{id:3,text:'Both'},{id:4,text:'None'}]
		});
	
	
	//Sectiontype dropdown
		$("#sectiontype"+newrowid).select2({
					placeholder: "Section",
					width:'120px',
					allowClear: true,
					data:[{id:1,text:'Filled'},{id:0,text:'Wall'},{id:0,text:'Open'}]
		});
	
	// Preinitialize dependent dropdowns
			 $("#filltype"+newrowid).select2({
				 placeholder: "Fill",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
			 
			 $("#fabrictype"+newrowid).select2({
				 placeholder: "Fabric",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
	
	// On Selecting Section Type, populate Fill Type
		$("#sectiontype"+newrowid).on("select2-selecting", function(e) { 
		
		 if(e.val=='1')
		 {
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=getFillTypeOptionsSelect2', // JQuery loads serverside.php
				  data: 'searchid=' + e.val,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					// Fill Type Drop Down
						$("#filltype"+newrowid).select2({
							placeholder: "Fill",
							allowClear: true,
							width:'120px',
							data:data
						});
				  }
			  });
		 } 
		 else {
			 // Fill Type Drop Down
			 $("#filltype"+newrowid).select2({
				 placeholder: "Fill",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
			 
			 $("#fabrictype"+newrowid).select2({
				 placeholder: "Fabric",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
			 
			 
			 $("#filltype"+newrowid).select2("val", 0);
			 $("#fabrictype"+newrowid).select2("val", 0);
			}
		//console.log ("selecting val="+ e.val+" e="+ JSON.stringify(e.object.text));
		})
	
	//On Selecting Fill Type, populate Fabric Type
		$("#filltype"+newrowid).on("select2-selecting", function(ft) { 
		
			  $.ajax({ // ajax call starts
				  url: 'ajaxfunctions.php?quoteaction=getFabricTypeOptionsSelect2', // JQuery loads serverside.php
				  data: 'searchid=' + ft.val,
				  dataType: 'json', // Choosing a JSON datatype
				  success: function(data) // Variable data contains the data we get from serverside
				  {
					// Fabric Type Drop Down
						$("#fabrictype"+newrowid).select2({
							placeholder: "Fabric",
							width:'120px',
							allowClear: true,
							data:data
						});
				  }
			  });
		//console.log ("selecting val="+ ft.val+" ft="+ JSON.stringify(ft.object.text));
		})

//get FRONT RAIL MATERIAL ----------------

	$.ajax({ // ajax call starts
	  url: 'ajaxfunctions.php?quoteaction=getFrontRailMaterialDropdown', // JQuery loads serverside.php
	  data: '',
	  dataType: 'json', // Choosing a JSON datatype
	  async: false, // Synchronous AJAX REQUESTTTTTTTTTTTTTTTTTTTTTTTT
	  success: function(data) // Variable data contains the data we get from serverside
	  {
		sdata = data;
	
	  }
	});
	
		var el = $('#archmaterial');
		var newOptions = '';
		
	  $.ajax({ // ajax call starts
		  url: 'ajaxfunctions.php?quoteaction=getArchMaterialDropdown', // JQuery loads serverside.php
		  data: '',
		  dataType: 'json', // Choosing a JSON datatype
		  async: false, // Non Ajax
		  success: function(data) // Variable data contains the data we get from serverside
		  {
			// ArchMaterial Drop Down
				for (var key in data) {
				  if (data.hasOwnProperty(key)) {
					newOptions += '<option value="'+data[key]['id']+'">'+data[key]['text']+'</option>';
					
				  }
				}
			el.select2('destroy').html(newOptions).select2();
			
			$(function() {
				el.select2().on('change', function() {
				
					$('#frontrailmaterial').removeClass('select2-offscreen').select2({
						data:sdata[$(this).val()-1],
						width:'140'
					});
					
					$('#backrailmaterial').removeClass('select2-offscreen').select2({
						data:sdata[$(this).val()-1],
						width:'140'
					});
				
				});
				
			});
		  }
	});



	//BRACKET TYPE SELECT 2 ---------------

	//Initialize Brackettype SELECT2 dropdown
			$("#brackettype"+newrowid).select2('destroy').html(newOptionsBT).select2({
					placeholder: "Bracket Type",
					width:'200px',
					allowClear: true,
					formatResult: formatbrackettypeResult,
					formatSelection: formatbrackettypeSelection,
			});

	
	} // Ending Initialize Dropdowns function
  
  // Format bracket type in select2
	function formatbrackettypeSelection(brackettype) {
		return brackettype.text; // optgroup
	}
	
	function formatbrackettypeResult(brackettype) {
		return "<img class='select2pic' src='images/brackets/" + brackettype.id.toLowerCase() + ".jpg'/>" + brackettype.text;
	}
	

