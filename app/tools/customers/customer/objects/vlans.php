<h4><?php print _("VLANs"); ?></h4>
<hr>
<span class="text-muted"><?php print _("All VLANs belonging to customer"); ?>.</span>

<script type="text/javascript">
/* fix for ajax-loading tooltips */
$('body').tooltip({ selector: '[rel=tooltip]' });
</script>


<?php

# only if set
if (isset($objects["vlans"])) {

	# get all VLANs and subnet descriptions
	$vlans = $objects['vlans'];

	# get custom VLAN fields
	$custom_fields = (array) $Tools->fetch_custom_fields('vlans');

	# set hidden fields
	$hidden_fields = json_decode($User->settings->hiddenCustomFields, true);
	$hidden_fields = is_array(@$hidden_fields['vlans']) ? $hidden_fields['vlans'] : array();

	# size of custom fields
	$csize = sizeof($custom_fields) - sizeof($hidden_fields);

	# set disabled for non-admins
	$disabled = $User->is_admin(false)==true ? "" : "hidden";


	# table
	print "<table class='table sorted vlans table-condensed table-top' data-cookie-id-table='customer_vlans'>";

	# headers
	print "<thead>";
	print '<tr">' . "\n";
	print ' <th data-field="number" data-sortable="true">'._('Number').'</th>' . "\n";
	print ' <th data-field="vlname" data-sortable="true">'._('Name').'</th>' . "\n";
	print ' <th data-field="name" data-sortable="true">'._('L2domain').'</th>' . "\n";
	if(sizeof(@$custom_fields) > 0) {
		foreach($custom_fields as $field) {
			if(!in_array($field['name'], $hidden_fields)) {
				print "	<th class='hidden-xs hidden-sm hidden-md'>".$Tools->print_custom_field_name ($field['name'])."</th>";
			}
		}
	}
    print "<th></th>";
	print "</tr>";
	print "</thead>";

	// body
	print "<tbody>";
	$m = 0;
	foreach ($vlans as $vlan) {

		// fixes
		$vlan->description = strlen($vlan->description)>0 ? " <span class='text-muted'>( ".$vlan->description." )</span>" : "";
		$vlan->domainDescription = strlen($vlan->domainDescription)>0 ? " <span class='text-muted'>( ".$vlan->domainDescription." )</span>" : "";

		// l2 domain
		$domain = $Tools->fetch_object ("vlanDomains", "id", $vlan->domainId);
		$domain_text = $domain===false ? "" : $domain->name." (".$domain->description.")";

		// start - VLAN details
		print "<tr class='$class change'>";
		print "	<td><a class='btn btn-xs btn-default' href='".create_link($_GET['page'], "vlan", $vlan->domainId, $vlan->vlanId)."'><i class='fa fa-cloud prefix'></i> ".$vlan->number."</a></td>";
		print "	<td><a href='".create_link($_GET['page'], "vlan", $vlan->domainId, $vlan->vlanId)."'>".$vlan->name."</a>".$vlan->description."</td>";
		print "	<td>".$domain_text."</td>";
        // custom fields - no subnets
        if(sizeof(@$custom_fields) > 0) {
	   		foreach($custom_fields as $field) {
		   		# hidden
		   		if(!in_array($field['name'], $hidden_fields)) {
					print "<td class='hidden-xs hidden-sm hidden-md'>";
					$Tools->print_custom_field ($field['type'], $vlan->{$field['name']});
					print "</td>";
				}
	    	}
	    }

        // actions
		print "	<td class='actions'>";
		print "	<div class='btn-group'>";
		if($User->user->edit_vlan=="Yes"||$User->is_admin(false)) {
		print "		<button class='btn btn-xs btn-default editVLAN' rel='tooltip' title='Edit object' data-action='edit'   data-vlanid='$vlan->vlanId'><i class='fa fa-pencil'></i></button>";
		print "		<button class='btn btn-xs btn-default editVLAN' rel='tooltip' title='Delete object' data-action='delete' data-vlanid='$vlan->vlanId'><i class='fa fa-times'></i></button>";
		if($User->get_module_permissions ("customers")>1)
		print "		<button class='btn btn-xs btn-default open_popup' rel='tooltip' title='Unlink object' data-script='app/admin/customers/unlink.php' data-class='700' data-object='vlans' data-id='$vlan->vlanId'><i class='fa fa-unlink'></i></button>";
		}
		print "	</div>";
		print "	</td>";

        print "</tr>";

		# next index
		$m++;
	}
	print "</tbody>";

	print '</table>';

}
else {
	$Result->show("info", _("No objects"));
}