var assignedleads,statusleads,sourcelead;

$(document).ready(function() {
           assignedleads= $('#assignedleads').DataTable({
                'ajax': 'php_actions/fetchDashboard.php',
                'order': [],
                lengthMenu: [[5, 10, 20], [5, 10, 20]]
            });
           statusleads= $('#statusleads').DataTable({
                'ajax': 'php_actions/fetchStatusLeads.php',
                'order': [],
                lengthMenu: [[20], [20]]
            });
           sourcelead= $('#Followuplead').DataTable({
                'ajax': 'php_actions/fetchCountFollowUp.php',
                'order': [],
                lengthMenu: [[20], [20]]
            });
           statusleads= $('#sourcelead').DataTable({
                'ajax': 'php_actions/fetchSourceLeads.php',
                'order': [],
                lengthMenu: [[20], [20]]
            });
	
});