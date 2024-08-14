(function($){
	$(document).ready(function(){
		$(document).on('click', '#load-report', triggerReportLoad);
		$(document).on('click', '#excel-btn', export_as_excel);
        $(document).on('click', '#pdf-btn', export_as_pdf);
	});
})(jQuery)

function triggerReportLoad()
{
	let
		el = $(this),
		formData = fieldData(),
		reportName = $('#report-name').val();

	$.ajax({
		url 		: '/report/'+reportName,
		method		: 'post',
		dataType 	: 'json',
		data		: formData,
		headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').val()
        },
		beforeSend 	: function(data){
			el.html(`<i class="icon-spinner2 spinner"></i> Loading...`).prop('disabled', true);
			$('#excel-btn').addClass('d-none');
            $('#pdf-btn').addClass('d-none');
			$('#jqGridContainer').html(`<div class="col-12 text-center"><i class="fa fa-spinner fa-2x fa-spin"></i></div>`);
		},
		success 	: render_current_report,
		error 	 	: function(error){
			$('#jqGridContainer').html(`<div class="col-12">
				<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-4x"></i> Unable to load report!</div>
			</div>`);
		},
        complete 	: function(){
			el.html(`<i class="fas fa-search"></i> Load`).prop('disabled', false);
			$("#jqGrid").jqGrid('filterToolbar',
			{
				stringResult : true,
				searchOnEnter: false,
				defaultSearch: "cn"
			});
			$("#jqGrid").jqGrid("navGrid","#jqGridPager", {
					edit 		: false,
					add 		: false,
					del 		: false,
					search 		: true,
					refresh		: true,
					position  	: "left",
					cloneToTop	: false
				},
				{},
				{},
				{},
				{
					multipleSearch: true,
					multipleGroup : true,
				}
			);
        }
	});
}


function render_current_report(res)
{

	$('#jqGridContainer').html(`<table id="jqGrid" style="position: relative;"></table><div id="jqGridPager"></div>`);
	columns = res.header;
	let cols = [];
	$.each(res.header, function(index, value) {
		let col = {
			label    : value[0],
			name     : index.trim(),
			align    : 'center',
			formatter: 'string'
		};
		cols.push(col);
	});

	$('#jqGrid').jqGrid({
		datatype: "local",
		data 	: res.data,
		colModel: cols,
		rowNum  			: 5000,
		rownumbers			: false,
		pager   			: '#jqGridPager',
		pgbuttons			: true,
		toppager			: false,
		height  			: res?.length>15? '500' : 'auto',
		width  				: '1560',
		headertitles        : false,
		gridview			: true,
		footerrow			: true,
		userDataOnFooter 	: true,
		viewrecords			: true,
		// sortname			: 'knicard_no',
		subGrid 			: false,
		subGridModel 		: [],
		responsive 			: true,
		multiselect			: false,
		grouping 			: false,
		groupingView 		: {

			// groupField 	   : ['order_no','buyer'],
			groupColumnShow: [true],
			groupText	   : [
				// "Buyer : <b>{0}</b>",
			],
			groupOrder	  : ["asc", "asc","asc"],
			groupSummary  : [false,false,false,true],
			groupCollapse : false
		},
		gridComplete	: grid_complete,
		colMenu     	: true,
		shrinkToFit 	: true,
		autowidth   	: true,
		colMenu 		: true

	});

	$('#excel-btn').removeClass('d-none');
    $('#pdf-btn').removeClass('d-none');
}

function grid_complete()
{
	let
		$grid       = $('#jqGrid'),
		footerObj	= {};

	$.each(columns, function(index, value) {
		if (value[1] == 'sum') {
			let
				sum     = 0,
				get = $grid.jqGrid('getCol', index.trim());

			$.each(get, function (i,v) {
				sum += parseFloat(v);
			});
			footerObj[index.trim()] = sum.toFixed(3);
		}
		else if(value[1] == 'unique') {
			let get = $grid.jqGrid('getCol', index.trim()),
				unique = [];
			for(var i=0; i<get.length; i++)
			{
				unique[get[i]]=(get[i]);
			}
			footerObj[index.trim()] = '[ '+(Object.keys(unique).length)+' ]';
		}
		else if(value[1] == 'length') {
			let get = $grid.jqGrid('getCol', index.trim());
			footerObj[index.trim()] = '[ '+(get.length)+' ]';
		}
	});

	$grid.jqGrid('footerData', 'set', footerObj);

	$('.ui-jqgrid').css('overflow-x', 'auto');
}

function export_as_excel(){
	if(confirm("Are You Want  To Export It into Excel Sheet ?"))
	{
		$("#jqGrid").jqGrid("exportToExcel",{
			includeLabels 		: true,
			includeGroupHeader  : true,
			includeFooter		: true,
			fileName 			: slug+'_'+new Date().getTime()+'.xlsx',
			maxlength 			: 40
		});
	}
}


function export_as_pdf(){
	if(confirm("Are You Want To Export It as a PDF ?"))
	{
		$("#jqGrid").jqGrid("exportToPdf",{
			title 				: reportTitle,
			orientation			: 'landscape',
			pageSize			: 'A4',
			onBeforeExport 		: function( doc ) {
				//you can set custom pdf styles here like this
				// alignment: 'center'

				doc.styles.tableHeader.fillColor	= '#dfeffc';
				doc.styles.tableHeader.color		= '#000';
				doc.styles.tableBody.fontSize 		= 6.3;
				doc.styles.tableHeader.fontSize 	= 8;
				doc.styles.tableFooter.color 		= '#000';
				doc.styles.tableFooter.fontSize 	= 8;
				doc.styles.tableFooter.bold 		= true;

				doc.header = function(currentPage, pageCount) {
					return { text: `Date Time: ${moment().format('Y:MM:DD hh:mm:ss')}`, alignment: 'right',fontSize:10, margin: [ 0, 5, 10,5 ]};
				};

				doc.footer = function(currentPage, pageCount,pageSize) {
					return [
						{ text: `${currentPage.toString() + ' of ' + pageCount}`, alignment: 'center',fontSize:10 }
					  ];
				};

				doc.pageMargins	=[ 30, 30, 30, 30 ];
				doc.defaultStyle= {
					columnGap: 20
				};
			},
			description			: '',
			customSettings		: null,
			download			: 'download',
			includeLabels 		: true,
			includeGroupHeader 	: true,
			includeFooter		: true,
			fileName 			: reportTitle.replaceAll(' ', '_') +moment().format('Y_MM_DD_hh_mm_ss')+".pdf",
	    });
	}
}


