var manageUserTable;
var checkbox = [];

if ($.validator && !$.validator.methods.extension) {
    $.validator.addMethod('extension', function(value, element, param) {
        if (this.optional(element)) {
            return true;
        }

        var pattern = typeof param === 'string' ? param.replace(/,/g, '|') : '';
        return new RegExp('\\.(' + pattern + ')$', 'i').test(value);
    }, 'Please enter a value with a valid extension.');
}

$(document).ready(function() {
	// top nav bar 
	$('#usersection').addClass('active');
	$('.dataTables_filter').addClass('pull-right');
	// manage product data table
	/* Initialize Bootstrap Datatables Integration */
            

            /* Initialize Datatables */
        fetch_data('no', '' , '', '', '');
        $('#reset').click(function(){
            $('#leaddatatable').DataTable().destroy();
            $('#roles-filter').val('');
            $('#experiancefilt').val('');
            $('#leadstatus').val('');
            $('#leadsource').val('');
            fetch_data('no', '' , '', '', '');
    });
         $(".dt-button").addClass("btn btn-primary");
});
$('#downloadsample').click(function(e) {
    e.preventDefault();  //stop the browser from following
    window.location.href = 'upload/sample_import_file.csv';
});
 function fetch_data(is_date_search, roles, experiance, leadstatus, leadsource)
             {

                       manageUserTable= $('#leaddatatable').DataTable({
                            processing: true,
                            serverSide: true,
                            paging: true,
                            pagingType: 'simple',
                            buttons: ['csv', 'excel', 'pdf' ],
                            'order': [],
                            columnDefs: [ { orderable: false, targets: [ 4 ] } ],
                            pageLength: 10,
                            lengthMenu: [[5, 10, 20, 50, 100], [5, 10, 20, 50, 100]],
                            language: {
                                paginate: {
                                    previous: 'Previous',
                                    next: 'Next'
                                }
                            },
                            dom: 'lBfrtip',
                            "ajax" : {
                                url:"php_actions/fetchMyLeads.php",
                                type:"POST",
                                data:{
                                 is_date_search:is_date_search,roles: roles, leadsource:leadsource, leadstatus:leadstatus , experiance: experiance
                                },
                                dataSrc: function(json) {
                                    console.log('fetchMyLeads page response:', json);
                                    if (json && json.error) {
                                        console.error('fetchMyLeads response error:', json);
                                    }
                                    return json && json.data ? json.data : [];
                                },
                                error: function(xhr, textStatus, errorThrown) {
                                    console.error('fetchMyLeads ajax error:', textStatus, errorThrown, xhr.responseText || xhr.statusText);
                                }
                               }
                        });
                       $('#leaddatatable').on('draw.dt', function () {
                            var pageInfo = $('#leaddatatable').DataTable().page.info();
                            console.log('fetchMyLeads page info:', pageInfo);
                       });
                       $(".dt-button").addClass("btn btn-primary");
            }
    $('#search').click(function(){
      var rolesfilter = $('#roles-filter').val();
      var experiance = $('#experiancefilt').val();
      var leadstatus = $('#leadstatus').val();
      var leadsource = $('#leadsource').val();
      //alert(rolesfilter+"   "+experiance+"   "+leadstatus+"   "+leadsource);
      if(rolesfilter != '' || leadsource !='' || leadstatus !='' || experiance!='')
      {
       $('#leaddatatable').DataTable().destroy();
       fetch_data('yes', rolesfilter, experiance, leadstatus, leadsource);
      }
      else
      {
       var growlType = 'danger';
       $.bootstrapGrowl('<p>Select the filters.</p>', {
            type: growlType,
            delay: 3000,
            allow_dismiss: true,
            offset: {from: 'top', amount: 20}
        });
      }
     }); 
	$(".adduser").click(function(){
		$("#userstable").addClass('hide');
		$("#addusers").removeClass('hide');

	});
	$("#usercancel").click(function(){
		$("#addusers").addClass('hide');
		$("#userstable").removeClass('hide');
	});
    $("#sortbyinterval").change(function(){ 
    var interval = $(this).val();
         $('#leaddatatable').DataTable().destroy();
         manageUserTable= $('#leaddatatable').DataTable({
                            processing: true,
                            serverSide: true,
                            paging: true,
                            pagingType: 'simple',
                            buttons: ['csv', 'excel', 'pdf' ],
                            'order': [],
                            columnDefs: [ { orderable: false, targets: [ 4 ] } ],
                            pageLength: 10,
                            lengthMenu: [[5, 10, 20, 50, 100], [5, 10, 20, 50, 100]],
                            language: {
                                paginate: {
                                    previous: 'Previous',
                                    next: 'Next'
                                }
                            },
                            dom: 'lBfrtip',
                            "ajax" : {
                                url:"php_actions/fetchByLastDays.php",
                                type:"POST",
                                data:{
                                 interval:interval
                                },
                                dataSrc: function(json) {
                                    console.log('fetchByLastDays page response:', json);
                                    if (json && json.error) {
                                        console.error('fetchByLastDays response error:', json);
                                    }
                                    return json && json.data ? json.data : [];
                                },
                                error: function(xhr, textStatus, errorThrown) {
                                    console.error('fetchByLastDays ajax error:', textStatus, errorThrown, xhr.responseText || xhr.statusText);
                                }
                               }
                        });
          $('#leaddatatable').on('draw.dt', function () {
                var pageInfo = $('#leaddatatable').DataTable().page.info();
                console.log('fetchByLastDays page info:', pageInfo);
          });
          $(".dt-button").addClass("btn btn-primary");
    });
    $("#example-chosen").change(function() {
        var sociallead='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div><div><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="color:#212121"><span style="font-family:verdana,sans-serif">Hello ,</span></span></span></span></span></span></div>&nbsp;<div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="color:#212121"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Social Media TL at DigiChefs. Feel free to get back in case of any queries.</span></span></span></span></span></span></div><div><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="color:#212121"><span style="font-family:verdana,sans-serif">Know more about us here:&nbsp;<a href="http://www.digichefs.com/" style="color:#1155cc" target="_blank">www.digichefs.com</a>.</span></span></span></span></span></span></div><div>&nbsp;</div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#212121"><strong>Social Media Team Lead</strong></span></span></span></span></span></span></div><div><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Managing a team of social media experts while guiding them through all social media responsibilities for the brands assigned to them</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Resolving queries raised by team mates or clients effectively</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Measuring performance of all the projects handled by the team and yourself and suggesting ways to enhance the share of voice for brands</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Thinking of monthly campaign ideas in participation with the copy &amp; graphic design teams, that you feel works best</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Role involves heavy client coordination &amp; interaction</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Preparing long term platform wise social media strategy for brands</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Generating ideas for moment marketing</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Providing training to team mates as and when necessary</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#212121"><strong>Requirement&nbsp;&nbsp;&nbsp;</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Good communication skills, written and spoken</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Ability to interact, communicate and present ideas</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Professionalism regarding time and deadlines</span></span></span></span></span></p></li></ul></div></div><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us.</strong></span></span></span></span></span></div></div></div></div></div></div></div></div>';
        var seolead='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a SEO Executive at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;<p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Perform keyword research in coordination with client business objectives&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Optimize keywords in existing content and uncover new opportunities for addition of keywords</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide recommendations and execute strategies for content development in coordination with <span style="color:#333333">SEO </span>goals<span style="color:#333333"> &ndash; general and keyword specific&nbsp;</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Keep pace with <span style="color:#333333">SEO, search engine, social media and internet marketing industry trends and developments</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Client interactions and monthly meetings to discuss SEO strategies</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide SEO analysis and recommendations in coordination with elements and structure of websites and web pages&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Implement link building campaigns in coordination with client SEO goals</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Monitor and administer Google Analytics dashboards and point out key areas of importance in accordance of client goals</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Communication to team and management on project development, timelines, and results</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Work closely with the other team members to meet client goals</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Bachelor&rsquo;s degree</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Significant Microsoft Excel experience preferred</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Basic knowledge of HTML preferred</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Good communication skills - written and spoken</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var bdintern='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a BD Intern at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;<p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Learning all about digital marketing and the importance of each service for brands in various business verticals</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Understanding marketing activities currently undertaken by prospects and identifying how DigiChefs can benefit the prospect via its services</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Conduct research to identify marketing tactics that can be pitched to prospects</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr">Working on MS Powerpoint to prepare roadmap &amp; plan of action for prospects</p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Meeting clients and understanding their feedback on the presentations, presented by senior sales team members</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Excellent communication and time management skills</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Good with MS Powerpoint basics and loves to make presentations on a day to day basis</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var clientser='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Client Servicing/Project Manager at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;<p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide end-to-end support to clients in the services rendered by DigiChefs to clients</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide end-to-end support to clients in the services rendered by DigiChefs to clients</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Collaborate with internal teams to design, develop and implement tactics in digital marketing</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr">Deliver projects on time ensuring quality standards are met</p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr">Monitor and report on Google Analytics metrics</p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr" style="text-align:justify"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Communicate with the team and ensure all members are on board with delegated tasks</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Excellent communication and time management skills</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Good with MS Powerpoint basics and loves to make presentations on a day to day basis</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Good team management skills</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var webintern='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Web Development Intern at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;<p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Building websites for clients using bootstrap HTML/CSS or CMS like Wordpress</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Writing well designed, testable, efficient code by using best software development practices&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Stay plugged into emerging technologies/industry trends and apply them into operations and activities</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Basic knowledge of HTML &amp; CSS</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Knowledge of Wordpress will help</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var fronddev='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Front End development at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;<p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">UI web development in HTML5, CSS3, and JavaScript technologies</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Experience in static website development is important. Knowledge of e-commerce website development is added benefit but not a must</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">You are able to work on improving site speed, Ajax calls, lazy loading, contact form setup, etc.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">You have experience building front end of websites in the past using HTML / CSS or CMS like Wordpress</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">You are interested in learning exciting new technologies</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Basic knowledge of HTML &amp; CSS</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Knowledge of Wordpress will help</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var hrintern='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of an HR Intern at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;<p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Assist in talent acquisition and recruitment processes</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Update our internal databases with new employee information, including contact details and employment forms &nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Conduct employee onboarding and help organize training &amp; development initiatives</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide support to employees in various HR-related topics such as leaves and compensation and resolve any issues that may arise</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Gather payroll data like leaves, working hours and bank accounts</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Promote HR programs to create an efficient and conflict-free workplace</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Assist in the development and implementation of human resource policies</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Organize half-yearly employee performance reviews</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Maintain employee files and records in electronic and paper form</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Enhance job satisfaction by resolving issues promptly, applying for new perks and benefits and organizing team building activities</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Good Communication Skills</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Problem Solving Aptitude</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var hrexecutive='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of an HR Executive at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;<p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Assist in talent acquisition and recruitment processes</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Update our internal databases with new employee information, including contact details and employment forms &nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Conduct employee onboarding and help organize training &amp; development initiatives</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide support to employees in various HR-related topics such as leaves and compensation and resolve any issues that may arise</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Gather payroll data like leaves, working hours and bank accounts</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Promote HR programs to create an efficient and conflict-free workplace</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Assist in the development and implementation of human resource policies</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Undertake tasks around performance/productivity management &amp; improvement of all employees</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Organize half yearly employee performance reviews</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Maintain employee files and records in electronic and paper form</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Enhance job satisfaction by resolving issues promptly, applying new perks and benefits and organizing team building activities</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Good Communication Skills</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Problem Solving Aptitude</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var paidmaneger='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Paid Manager at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span><br/>&nbsp;</p><p><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Overview:</strong></span></span></span></span></span></span></p><p>The Senior Media Manager is responsible for planning, optimizing, and reporting campaigns to meet and surpass the clients&rsquo; success metrics. He/she understands has exposure to Google Ads OR Facebook Ads OR both.</p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Assisting day-to-day campaign execution along with the rest of the team members.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Major work on Google Ads or Facebook Ads on a day to day basis.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Using tools like Google Analytics to understand the progress of campaigns and suggest optimizations.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Owns all aspects of campaign management from the initial planning, brainstorming, utilization of 3rd party tools to building out reports.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Working in tandem with the client&rsquo;s business goals to deliver ROI for clients</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Working on media planning on a month to month basis for assigned clients to ensure that we&rsquo;re meeting long term objectives designated by client</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Monitor and evaluate performance across the tools &amp; channels in the scope of work for DigiChefs.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Communicates to team and management on project development, timelines, and results.</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Bachelor&rsquo;s degree</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">2-3 years of experience in Google Ads or Facebook Ads</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Agency experience preferred</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Significant Microsoft Excel experience preferred</span></span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#222222">Understands campaign objectives/strategy and is able to present an insight in reports to clients as well as field/anticipate questions.</span></span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var semex='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of an SEM Executive at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Researching keywords</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Help strategize and execute search ads, display ads and retargeting campaigns</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Executing and monitoring tests (ad copy, landing page, etc)</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Work with team members to develop and understand SEM strategies that will help in meeting client&rsquo;s business objectives</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Major work on Google Ads or Facebook Ads on a day to day basis.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Using tools like Google Analytics to understand progress of campaigns and suggest optimizations.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Owns all aspects of campaign management from the initial planning, brainstorming, utilization of 3rd party tools to building out reports.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Working in tandem with client&rsquo;s business goals to deliver ROI for clients</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Working on media planning on a month to month basis for assigned clients to ensure that we&rsquo;re meeting long term objectives designated by client</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Monitor and evaluate performance across the tools &amp; channels in scope of work for DigiChefs.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Communicates to team and management on project development, timelines, and results.</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Bachelor&rsquo;s degree</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">1-2 years of experience in Google Ads or Facebook Ads</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Agency experience preferred</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Significant Microsoft Excel experience preferred</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Understands campaign objectives/strategy and is able to present an insight in reports to clients as well as field/anticipate questions. </span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var seointern='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of an SEO Intern at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Perform keyword research in coordination with client business objectives&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Optimize keywords in existing content and uncover new opportunities for addition of keywords</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide recommendations and execute strategies for content development in coordination with SEO goals &ndash; general and keyword specific&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Implement link building campaigns in coordination with client SEO goals</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Keep pace with SEO, search engine, social media and internet marketing industry trends and developments</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Communication to team and management on project development, timelines, and results</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Work closely with the other team members to meet client goals</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Bachelor&rsquo;s degree</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Significant Microsoft Excel experience preferred</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Basic knowledge of HTML preferred</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Good communication skills - written and spoken</span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var seoexecutive='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of an SEO Executive at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Perform keyword research in coordination with client business objectives&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Optimize keywords in existing content and uncover new opportunities for addition of keywords</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide recommendations and execute strategies for content development in coordination with SEO goals &ndash; general and keyword specific&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Keep pace with SEO, search engine, social media and internet marketing industry trends and developments</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Client interactions and monthly meetings to discuss SEO strategies</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Provide SEO analysis and recommendations in coordination with elements and structure of websites and web pages&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Implement link building campaigns in coordination with client SEO goals</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Monitor and administer Google Analytics dashboards and point out key areas of importance in accordance of client goals</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Communication to team and management on project development, timelines, and results</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Work closely with the other team members to meet client goals</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Bachelor&rsquo;s degree</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Significant Microsoft Excel experience preferred</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Basic knowledge of HTML preferred</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Good communication skills - written and spoken</span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var copyintern='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of an Intern Copywriter at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Understanding the client&#39;s core product or service, the target audience &amp; market, the problem that the product or service intends to solve</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Campaign planning on a month to month basis that goes with client&rsquo;s business objectives</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Engaging in ideation &amp; copywriting under the guidance of senior executives</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Brainstorming visual and copy ideas with other members of the creative team</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Presenting and curating copy ideas to client satisfaction</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Excellent communication skills</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">A persuasive and confident approach to creativity</span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var copyex='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of an Senior Copywriter at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Understanding client&#39;s core product or service, the target audience &amp; market, the problem that the product or service intends to solve</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Campaign planning on a month to month basis that go with client&rsquo;s business objectives</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Coming up with ideas that are forward-thinking and in-line with current media trends &amp; client&rsquo;s personality &amp; business objectives</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Demonstrate a clear understanding of campaign objectives and devise creative communication strategies to achieve them</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Writing copies following brand guidelines</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Offer creative ideas and encourage others to share their ideas</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Edit and fact-check pieces of content generated by colleagues and offer feedback when necessary &nbsp; &nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Push clients towards fresh, exciting ideas</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Significant experience in professional, commercial writing</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">A persuasive and confident approach to creativity</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Excellent communication skills</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Keen attention to detail&nbsp;</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Confidence in building a rapport with clients and nurturing good working relationship</span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var gd='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Graphic Designer at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Working with Adobe Suite (Photoshop, Illustrator, etc.).</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Thinking creatively and developing new design concepts, graphics, and layouts.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Take the design &ldquo;brief&rdquo; to record requirements and clients&#39; needs.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Prepare rough drafts and present your ideas.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Amend final designs to clients&#39; comments.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Work as part of a team with copywriters, designers &amp; social media experts.</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Possession of creative flair, versatility, conceptual/visual ability and originality.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Demonstrable graphic design skills with a strong portfolio.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Ability to interact, communicate and present ideas.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Up to date with industry-leading software and technologies &amp; intent to learn the same</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Professionalism regarding time and deadlines.</span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var sma='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Social Media Analyst at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Understanding the client&#39;s core product or service, the target audience &amp; market, the problem that the product or service intends to solve</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Managing end to end social media presence for brands, social listening &amp; reputation management</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Thinking of monthly campaign ideas in participation with the copy &amp; graphic design teams</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Heavy client coordination &amp; interaction</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Preparing long term platform wise social media strategy for brands</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Generating ideas for moment marketing</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Monthly sentiment analysis &amp; reporting for brands you handle</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Good communication skills, written and spoken.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Ability to interact, communicate and present ideas.</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Professionalism regarding time and deadlines.</span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        var smalead='<div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div class="gmail_default" style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div class="gmail_quote"><div dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Hello,</span></span></span></span></span></div><div dir="ltr"><p><br/><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">We are looking for a candidate to fill in the vacancy of a Social Media Team Lead at DigiChefs. Feel free to get back in case of any queries.<br/><br/>Know more about us here:&nbsp;<a href="https://www.google.com/url?q=http://www.digichefs.com&amp;sa=D&amp;ust=1519123416310000&amp;usg=AFQjCNFrFPjjtLEwbA51zpAinfWu6o12wQ" style="color:#1155cc" target="_blank">www.digichefs.com</a></span></span></span></span></span></p><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><span style="color:#333333"><strong>Job Description:</strong></span></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Managing a team of social media experts while guiding them through all social media responsibilities for the brands assigned to them</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Resolving queries raised by team mates or clients effectively</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Measuring performance of all the projects handled by the team and yourself and suggesting ways to enhance the share of voice for brands</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Thinking of monthly campaign ideas in participation with the copy &amp; graphic design teams, that you feel works best</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Role involves heavy client coordination &amp; interaction</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Preparing long term platform wise social media strategy for brands</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Generating ideas for moment marketing</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Providing training to team mates as and when necessary</span></span></span></span></span></p></li></ul><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Requirements:</strong></span></span></span></span></span></p><ul><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Good communication skills, written and spoken</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Ability to interact, communicate and present ideas</span></span></span></span></span></p></li><li dir="ltr" style="list-style-type:disc"><p dir="ltr"><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif">Professionalism regarding time and deadlines</span></span></span></span></span></p></li></ul></div></div></div></div></div></div></div></div></div><div style="-webkit-text-stroke-width:0px; text-align:start"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div dir="ltr"><div><span style="font-size:small"><span style="color:#222222"><span style="font-family:verdana,sans-serif"><span style="background-color:#ffffff"><span style="font-family:verdana,sans-serif"><strong>Please share your resume with us</strong></span></span></span></span></span></div></div></div></div></div></div></div></div></div></div>';
        if($( "#example-chosen").val()=="1"){
            CKEDITOR.instances['textarea-ckeditor'].setData(sociallead);
        }else if($( "#example-chosen").val()=="2"){
            CKEDITOR.instances['textarea-ckeditor'].setData(seolead);
        }else if($( "#example-chosen").val()=="3"){
             CKEDITOR.instances['textarea-ckeditor'].setData(bdintern);
        }else if($( "#example-chosen").val()=="4"){
             CKEDITOR.instances['textarea-ckeditor'].setData(clientser);
        }
        else if($( "#example-chosen").val()=="5"){
             CKEDITOR.instances['textarea-ckeditor'].setData(webintern);
        }
        else if($( "#example-chosen").val()=="6"){
             CKEDITOR.instances['textarea-ckeditor'].setData(fronddev);
        }
        else if($( "#example-chosen").val()=="17"){
             CKEDITOR.instances['textarea-ckeditor'].setData(hrintern);
        }
         else if($( "#example-chosen").val()=="7"){
             CKEDITOR.instances['textarea-ckeditor'].setData(hrexecutive);
        }
        else if($( "#example-chosen").val()=="8"){
             CKEDITOR.instances['textarea-ckeditor'].setData(paidmaneger);
        }
        else if($( "#example-chosen").val()=="9"){
             CKEDITOR.instances['textarea-ckeditor'].setData(semex);
        }
        else if($( "#example-chosen").val()=="10"){
             CKEDITOR.instances['textarea-ckeditor'].setData(seointern);
        }
        else if($( "#example-chosen").val()=="11"){
             CKEDITOR.instances['textarea-ckeditor'].setData(seoexecutive);
        }
         else if($( "#example-chosen").val()=="12"){
             CKEDITOR.instances['textarea-ckeditor'].setData(copyintern);
        }
         else if($( "#example-chosen").val()=="13"){
             CKEDITOR.instances['textarea-ckeditor'].setData(copyex);
        }
         else if($( "#example-chosen").val()=="14"){
             CKEDITOR.instances['textarea-ckeditor'].setData(gd);
        }
         else if($( "#example-chosen").val()=="15"){
             CKEDITOR.instances['textarea-ckeditor'].setData(sma);
        }
         else if($( "#example-chosen").val()=="16"){
             CKEDITOR.instances['textarea-ckeditor'].setData(smalead);
        }
    });
    function clearChosen() {
    
}
function sendmail() {
            checkbox = [];
            $.each($("input[name='leadcheckbox']:checked"), function(){            
                checkbox.push($(this).val());
            });
            // $("#mailform").get(0).reset();

            $("#mailform")[0].reset();
            CKEDITOR.instances['textarea-ckeditor'].setData('');
             // $('#example-chosen').val(null).trigger('change');
            $('#email-chosen-multiple').trigger('chosen:updated');
            if(checkbox.length <1){
                var growlType = 'danger';
                $.bootstrapGrowl('<p>Select the checkboxes!</p>', {
                    type: growlType,
                    delay: 3000,
                    allow_dismiss: true,
                    offset: {from: 'top', amount: 20}
                });

            }else{
                $('#email-chosen-multiple').trigger('chosen:updated');
                $("#email-chosen-multiple").val(checkbox).trigger("chosen:updated");
                 //alert("My favourite sports are: " + checkbox.join(", "));
                //  $.ajax({
                //    type: "POST",
                //    data: {checkbox:checkbox},
                //    url: "php_actions/assignedLeads.php",
                //    success: function(response){
                //     var response = JSON.parse(response);
                //     if(response.success==true){
                //      var growlType = 'success';
                //         $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                //             type: growlType,
                //             delay: 3000,
                //             allow_dismiss: true,
                //             offset: {from: 'top', amount: 20}
                //         });
                //     }else{
                //         var growlType = 'danger';
                //         $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                //             type: growlType,
                //             delay: 3000,
                //             allow_dismiss: true,
                //             offset: {from: 'top', amount: 20}
                //         });
                //     }
                //         manageLeadsTable.ajax.reload(null, false);
                   
                //    }
                // });
            }
}

$('#mailform').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                ignore: ":hidden:not(select)",
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                    
                },
                rules: {
                    'textarea-ckeditor':{
                        required: true
                    },
                    'email-chosen-multiple[]':{
                         required: true
                    },
                    'subject':{
                         required: true
                    }
                },
                messages: {
                    'textarea-ckeditor':{
                        required: "Enter mail comtent"
                    },
                    'subject':{
                         required: "Enter mail subject."
                    },
                    'email-chosen-multiple[]':{
                         required: "Select atleast one email."
                    },
                },
                 submitHandler: function(form) {
                                    //var formactions = $(form); 
                                    var mailcontents = CKEDITOR.instances['textarea-ckeditor'].getData()
                                    var subject = $("#subject").val();
                                    var value =$("#email-chosen-multiple").val();
                                    $.ajax({
                                        type: 'POST',
                                        data: {checkbox:value, subject:subject, mailcontents:mailcontents},
                                        url : 'php_actions/sendmail.php',
                                        success:function(response) {
                                                console.log(response);
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#emailmodal').modal('hide');
                                                    $("#mailform")[0].reset();
                                                    CKEDITOR.instances['textarea-ckeditor'].setData('');
                                                     // $('#example-chosen').val(null).trigger('change');
                                                     $('#email-chosen-multiple').val('').trigger("change");
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                } 
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });
function editMyLead(Id = null) {
    $('#editleadform').get(0).reset();
    gettimeline(Id);
    getcomment(Id);
    if(Id) {
        $.ajax({
            url: 'php_actions/fetchMyLeadsbyId.php',
            type: 'post',
            data: {Id: Id},
            dataType: 'json',
            success:function(response) {  
               //console.log(response);
                $("#editlead-name").val(response.name);
                $("#editlead-email").val(response.email);
                $("#editlead-phone").val(response.phonenumber);
                $("#editleadadded").val(response.dateadded);
                $("#editleadstatus").val(response.statusid);
                $("#editlead-source").val(response.sourceid);
                $("#editleadlast").val(response.lastcontact);
                $("#editlead-followup").val(response.followup);
                $("#Editlead-id").val(response.id);
                $("#editstreet").val(response.street);
                $("#editcountry").val(response.country);
                $("#editcity").val(response.city);
                $("#editpincode").val(response.zip);
                $("#editexperience").val(response.experiance);
                $("#editqualification").val(response.qualification);
                $("#editcjob").val(response.cjtitle);
                $("#editcemployer").val(response.cemployer);
                $("#editexpected").val(response.esalary);
                $("#editcsalary").val(response.csalary);
                $("#editskillset").val(response.skillset);
                $("#info").val(response.ainfo);
                $("#editlead-reference").val(response.referral);
                //$("#editexample-chosen-multiple").val(response.roles);

               
                $("#notice").val(response.nperiod);
                 var my_val = response.roles;
                var str_array = my_val.split(',');
                $(".mselect").val(str_array).trigger("chosen:updated");
                var resume = response.resume || '';
                var resumeUrl = 'view_resume.php?file=' + encodeURIComponent(resume);
                var extension = resume.substr( (resume.lastIndexOf('.') +1) );
                if(extension=="pdf" || extension=="PDF"){
                    $("#resume").html('<div class="pdfion"><img src="img/icon.png" /></div><a href="' + resumeUrl + '" target="_blank">resume</a>');
                }
                if(extension=="doc" || extension=="DOC" || extension=="docx" || extension=="DOCX"){
                    $("#resume").html('<div class="pdfion"><img src="img/doc-icon.png" /></div><a href="' + resumeUrl + '" target="_blank">resume</a>');
                }else if(!extension.trim()) {
                    $("#resume").empty();
                }
            }
        });
    }
}
function gettimeline(id){
    if(id) {
        $(".timeline-list").empty();
        $.ajax({
            url: 'php_actions/getTimeline.php',
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success:function(response) {  
                    $(".timeline-list").empty();
                    if (response.data && response.data.length) {
                        $.each(response.data, function(index, item) {
                            $(".timeline-list").append(item);
                        });
                    }

            }
        });
    }
}
$("#editleadstatus").on("change", function () {
    $('#editlead-commentbox').val('');
});
function getcomment(id){

    if(id) {

        $.ajax({
            url: 'php_actions/getLastComment.php',
            type: 'post',
            data: {id: id},
            dataType: 'json',
            success:function(response) {  
            console.log(response);
                    $("#editlead-commentbox").val(response.additional_data);
            }
        });
    }
}
$('#addcandidate').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                    
                },
                rules: {
                    'name': {
                        required: true
                    },
                    'phone':{
                        required: true,
                        number: true
                    },
                    'email': {
                        required: true,
                        email: true
                    },
                    'source': {
                        required: true
                    },
                    'pincode':{
                        number: true
                    },
                    'example-file-input':{
                        extension: "docx|rtf|doc|pdf"
                    }
                },
                messages: {
                    'editlead-name': {
                        required: 'Please enter a name'
                    },
                    'editlead-email': 'Please enter a valid email address',
                    'editlead-phone':  {
                        required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    },
                    'editleadstatus': {
                        required : 'Please enter a password'
                    },
                    'editlead-source': {
                        required: 'Please select the source'
                    },
                    'example-file-input':{
                        extension: "File format is not valid. Please use pdf or doc format."
                    }
                },
                 submitHandler: function(form) {
                        if( document.getElementById("example-file-input").files.length == 0 ){
                            $("#addcandidate").removeAttr("enctype");
                            var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/createlead.php',
                                        type: formactions.attr('method'),
                                        data: formData,
                                        cache: false,
                                        processData: false,
                                        success:function(response) {
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#addLeadModal').modal('hide');
                                                    $('#addcandidate').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageUserTable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                        }else{
                            $('#addcandidate').attr("enctype", "multipart/form-data");
                            var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/createlead.php',
                                        type: formactions.attr('method'),
                                        data: new FormData(form),
                                        cache: false,
                                        processData: false,
                                        contentType: false,
                                        success:function(response) {
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#addLeadModal').modal('hide');
                                                     $('#addcandidate').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageUserTable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                    }
                                    
                 }
            });
$('#editleadform').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                    
                },
                rules: {
                    'editlead-name': {
                        required: true
                    },
                    'editlead-phone':{
                        required: true,
                        number: true
                    },
                    'editlead-email': {
                        required: true,
                        email: true
                    },
                    'editleadstatus':{
                        required: true,
                    },
                    'editlead-source':{
                        required: true
                    }
                },
                messages: {
                    'editlead-name': {
                        required: 'Please enter a name'
                    },
                    'editlead-email': 'Please enter a valid email address',
                    'editlead-phone':  {
                        required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    },
                    'editleadstatus': {
                        required : 'Please enter a password'
                    },
                    'editlead-source': {
                        required: 'Please select the source'
                    }
                },
                 submitHandler: function(form) {
                    if(document.getElementById("editexample-file-input").files.length == 0 ){
                                    $("#editleadform").removeAttr("enctype");
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/updateLeadStatus.php',
                                        type: formactions.attr('method'),
                                        data: formData,
                                        cache: false,
                                        processData: false,
                                        success:function(response) {
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#editMyLeadModal').modal('hide');
                                                    //$('#editleadform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageUserTable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                                }else{
                                    $('#editleadform').attr("enctype", "multipart/form-data");
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/updateLeadStatus.php',
                                        type: formactions.attr('method'),
                                        data: new FormData(form),
                                        cache: false,
                                        processData: false,
                                        contentType: false,
                                        success:function(response) {
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#editMyLeadModal').modal('hide');
                                                    //$('#editleadform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageUserTable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                                }
                 }
            });
            $('#EditUserForm').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                    
                },
                rules: {
                    'Edituser-fname': {
                        required: true
                    },
                    'Edituser-lname': {
                        required: true
                    },
                    'Edituser-phone':{
                        required: true,
                        number: true
                    },
                    'Edituser-email': {
                        required: true,
                        email: true
                    },
                    'Edituser-password': {
                        required: true,
                        minlength: 8
                    },
                    'Edituser-passwordconfirm': {
                        equalTo: '#Edituser-password'
                    },
                    'edit-action':{
                        required: true,
                    },
                    'edit-rights':{
                        required: true
                    }
                },
                messages: {
                    'Edituser-fname': {
                        required: 'Please enter a first name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'Edituser-lname': {
                        required: 'Please enter a last name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'Edituser-email': 'Please enter a valid email address',
                    'Edituser-phone':  {
                        required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    },
                    'Edituser-password': {
                        required : 'Please enter a password',
                        minlength: 'Your password must be at least 8 characters long'
                    },
                    'Edituser-passwordconfirm': {
                        minlength: 'Your password must be at least 8 characters long',
                        equalTo: 'Please enter the same password as above'
                    },
                    'edit-rights': {
                        required: 'Select the rights'
                    },
                    'edit-action':{
                        required: 'Select the status',
                    }

                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/UpdateSelectedUser.php',
                                        type: formactions.attr('method'),
                                        data: formData,
                                        cache: false,
                                        processData: false,
                                        success:function(response) {
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#addUserModal').modal('hide');
                                                    $('#adduserform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageUserTable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });

$('#importLeadform').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                    
                },
                rules: {
                    'leadfile': {
                        required: true,
                        extension: "xls|csv"
                    },
                    'import-leadsource': {
                        required: true
                    },
                    'import-leadassigned':{
                        required: true
                    }
                },
                messages: {
                    'leadfile': {
                        required: 'File is required.',
                        extension: 'Please enter a value with a valid extension.'
                    },
                    'import-leadsource': {
                        required: 'Select the source of the leads.'
                    },
                    'import-leadassigned': {
                        required: 'Select the center to assigned the lead.'
                    }
                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    $.ajax({
                                        url : 'php_actions/importleads.php',
                                        type: formactions.attr('method'),
                                        data: new FormData(form),
                                        contentType: false,
                                        cache: false,
                                        processData:false,
                                        success:function(response) {
                                                console.log(response);
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#addLeadImportModal').modal('hide');
                                                    $('#importLeadform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageUserTable.ajax.reload(null, false); 
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });
