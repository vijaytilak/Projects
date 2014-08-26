
/*------------JQUERY onReady Functions ------------------*/

		$(document).ready(function () {

			initializeDropDowns('1'); 
			
		    //Prepare jTable
			$('#FrontTableContainer').jtable({
				title: 'Canopy - Front Side Configuration',
				paging: true,
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
											plength: record.plength,
											pheight: record.pheight,
											support: record.support,
											postposition: record.postposition,
											postspacing: record.postspacing,
											brackets: record.brackets,
											bracketspacing: record.bracketspacing,
											sectiontype: record.sectiontype,
											filltype: record.filltype,
											fabrictype: record.fabrictype,
											rollerblinds: record.rollerblinds,
										}
									});
								});
							} else {
								//No rows selected
								$('#MessageContainer').append('<div style="padding:5px">No row selected!</div>');
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
					plength: {
						title: 'Length',
						inputClass: 'validate[required,custom[number]]',
						width :'6%',
						sorting: false
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
					postspacing: {
						title: 'PostSpc',
						width :'7%',
						sorting: false
					},
					postposition: {
						title: 'PostPos.',
						options: [{ Value: '1', DisplayText: 'Left' }, { Value: '2', DisplayText: 'Right' }, { Value: '3', DisplayText: 'Both' }, { Value: '4', DisplayText: 'None' }],
						list: true,
						width :'7%',
						sorting: false
					},
					brackets: {
						title: 'Brackets',
						width :'7%',
						sorting: false
						},
					bracketspacing: {
						title: 'BrktSpc',
						width :'7%',
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
						width :'12%'
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
						width :'15%',
						sorting: false
					},
					rollerblinds: {
						title: 'RollrBlnd',
						type: 'checkbox',
						values: { 'false': 'No', 'true': 'Yes' },
						defaultValue: 'false',
						width :'7%',
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
				
				//Register to selectionChanged event to hanlde events
				selectionChanged: function () {
					//Get all selected rows
					var $selectedRows = $('#FrontTableContainer').jtable('selectedRows');
	 
					$('#MessageContainer').empty();
					if ($selectedRows.length > 0) {
						//Show selected rows
						$selectedRows.each(function () {
							var record = $(this).data('record');
							$('#MessageContainer').empty();
							$('#MessageContainer').append('<div style="padding:5px">'+$selectedRows.length+' rows selected!</div>');
						});
					} else {
						//No rows selected
						$('#MessageContainer').empty();
						$('#MessageContainer').append('<div style="padding:5px">No row selected!</div>');
					}
				},
				rowInserted: function (event, data) {},
								
			});

			//Delete selected students
			$('#DeleteAllButton').button().click(function () {
				var $selectedRows = $('#FrontTableContainer').jtable('selectedRows');
				$('#FrontTableContainer').jtable('deleteRows', $selectedRows);
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
														sectiontag: record.sectiontag,
														plength: record.plength,
														pheight: record.pheight,
														support: record.support,
														postposition: record.postposition,
														postspacing: record.postspacing,
														brackets: record.brackets,
														bracketspacing: record.bracketspacing,
														sectiontype: record.sectiontype,
														filltype: record.filltype,
														fabrictype: record.fabrictype,
														rollerblinds: record.rollerblinds,
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

									if ($("#postspacing1").val())
									jsobObj.record.postspacing = $("#postspacing1").val();

									if ($("#brackets1").val())
									jsobObj.record.brackets = $("#brackets1").val();

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
								$('#MessageContainer').empty();
								$('#MessageContainer').append('<div style="padding:5px">No row selected!</div>');
							}
										
			});

			//Load person list from server
			$('#FrontTableContainer').jtable('load');

		});
	
/*------------Ending JQUERY onReady Functions ------------------*/


/*------------Common Functions ------------------*/

/****************Chained Select2 Dropdowns******************/

	function initializeDropDowns(newrowid) {		 
	//SupportType dropdown
		$("#supporttype"+newrowid).select2({
					placeholder: "Select Support",
					width:'130px',
					allowClear: true,
					data:[{id:1,text:'Posts'},{id:2,text:'Brackets'},{id:0,text:'None'}]
		});
		
		
	//PostPostion dropdown
		$("#postposition"+newrowid).select2({
					placeholder: "Select PostPos",
					width:'130px',
					allowClear: true,
					data:[{id:1,text:'Left'},{id:2,text:'Right'},{id:3,text:'Both'},{id:4,text:'None'}]
		});
	
	
	//Sectiontype dropdown
		$("#sectiontype"+newrowid).select2({
					placeholder: "Select Section",
					width:'120px',
					allowClear: true,
					data:[{id:1,text:'Filled'},{id:0,text:'Wall'},{id:0,text:'Open'}]
		});
	
	// Preinitialize dependent dropdowns
			 $("#filltype"+newrowid).select2({
				 placeholder: "Select Fill",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
			 
			 $("#fabrictype"+newrowid).select2({
				 placeholder: "Select Fabric",
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
							placeholder: "Select Fill",
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
				 placeholder: "Select Fill",
				 width:'120px',
				 allowClear: true,
				 data: [{id:0,text:'none'}]	 
			 });
			 
			 $("#fabrictype"+newrowid).select2({
				 placeholder: "Select Fabric",
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
							placeholder: "Select Fabric",
							width:'120px',
							allowClear: true,
							data:data
						});
				  }
			  });
		//console.log ("selecting val="+ ft.val+" ft="+ JSON.stringify(ft.object.text));
		})
	
					 
	}
  
/****************Ending Chained Select2 Dropdowns******************/			 
