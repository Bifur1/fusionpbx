<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2024
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/check_auth.php";
	require_once "xml_cdr_statistics_inc.php";

//check permissions
	if (permission_exists('xml_cdr_statistics')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//set default showall
	$show_all = false;

//additional includes
	$document['title'] = $text['title-call-statistics'];
	require_once "resources/header.php";

//search url
	$search_url = '';
	if (permission_exists('xml_cdr_search_advanced')) {
		$search_url .= '&redirect=xml_cdr_statistics';
	}
	if(permission_exists('xml_cdr_all') && (isset($_GET['showall']) && $_GET['showall'] === 'true')){
		$search_url .= '&showall=true';
		$show_all = true;
	}
	if (!empty($_GET['direction'])) {
		$search_url .= '&direction='.urlencode($_GET['direction']);
	}
	if (!empty($_GET['leg'])) {
		$search_url .= '&leg='.urlencode($_GET['leg']);
	}
	if (!empty($_GET['caller_id_name'])) {
		$search_url .= '&caller_id_name='.urlencode($_GET['caller_id_name']);
	}
	if (!empty($_GET['caller_extension_uuid'])) {
		$search_url .= '&caller_extension_uuid='.urlencode($_GET['caller_extension_uuid']);
	}
	if (!empty($_GET['caller_id_number'])) {
		$search_url .= '&caller_id_number='.urlencode($_GET['caller_id_number']);
	}
	if (!empty($_GET['destination_number'])) {
		$search_url .= '&destination_number='.urlencode($_GET['destination_number']);
	}
	if (!empty($_GET['context'])) {
		$search_url .= '&context='.urlencode($_GET['context']);
	}
	if (!empty($_GET['start_stamp_begin'])) {
		$search_url .= '&start_stamp_begin='.urlencode($_GET['start_stamp_begin']);
	}
	if (!empty($_GET['start_stamp_end'])) {
		$search_url .= '&start_stamp_end='.urlencode($_GET['start_stamp_end']);
	}
	if (!empty($_GET['answer_stamp_begin'])) {
		$search_url .= '&answer_stamp_begin='.urlencode($_GET['answer_stamp_begin']);
	}
	if (!empty($_GET['answer_stamp_end'])) {
		$search_url .= '&answer_stamp_end='.urlencode($_GET['answer_stamp_end']);
	}
	if (!empty($_GET['end_stamp_begin'])) {
		$search_url .= '&end_stamp_begin='.urlencode($_GET['end_stamp_begin']);
	}
	if (!empty($_GET['end_stamp_end'])) {
		$search_url .= '&end_stamp_end='.urlencode($_GET['end_stamp_end']);
	}
	if (!empty($_GET['duration'])) {
		$search_url .= '&duration='.urlencode($_GET['duration']);
	}
	if (!empty($_GET['billsec'])) {
		$search_url .= '&billsec='.urlencode($_GET['billsec']);
	}
	if (!empty($_GET['hangup_cause'])) {
		$search_url .= '&hangup_cause='.urlencode($_GET['hangup_cause']);
	}
	if (!empty($_GET['uuid'])) {
		$search_url .= '&uuid='.urlencode($_GET['uuid']);
	}
	if (!empty($_GET['bleg_uuid'])) {
		$search_url .= '&bleg_uuid='.urlencode($_GET['bleg_uuid']);
	}
	if (!empty($_GET['accountcode'])) {
		$search_url .= '&accountcode='.urlencode($_GET['accountcode']);
	}
	if (!empty($_GET['read_codec'])) {
		$search_url .= '&read_codec='.urlencode($_GET['read_codec']);
	}
	if (!empty($_GET['write_codec'])) {
		$search_url .= '&write_codec='.urlencode($_GET['write_codec']);
	}
	if (!empty($_GET['remote_media_ip'])) {
		$search_url .= '&remote_media_ip='.urlencode($_GET['remote_media_ip']);
	}
	if (!empty($_GET['network_addr'])) {
		$search_url .= '&network_addr='.urlencode($_GET['network_addr']);
	}
	if (!empty($_GET['mos_comparison'])) {
		$search_url .= '&mos_comparison='.urlencode($_GET['mos_comparison']);
	}
	if (!empty($_GET['mos_score'])) {
		$search_url .= '&mos_score='.urlencode($_GET['mos_score']);
	}

//show the content
	echo "<div class='action_bar' id='action_bar'>\n";
	echo "	<div class='heading'><b>".$text['title-call-statistics']."</b></div>\n";
	echo "	<div class='actions'>\n";
	if (substr_count($_SERVER['HTTP_REFERER'], 'app/xml_cdr/xml_cdr.php') != 0) {
		echo button::create(['type'=>'button','label'=>$text['button-back'],'icon'=>$settings->get('theme', 'button_icon_back'),'id'=>'btn_back','style'=>'margin-right: 15px;','link'=>'xml_cdr.php']);
	}
	if (permission_exists('xml_cdr_search_advanced')) {
		echo button::create(['type'=>'button','label'=>$text['button-advanced_search'],'icon'=>'tools','link'=>'xml_cdr_search.php?type=advanced'.$search_url]);
	}
	if (permission_exists('xml_cdr_all') && !$show_all) {
		echo button::create(['type'=>'button','label'=>$text['button-show_all'],'icon'=>$settings->get('theme', 'button_icon_all'),'link'=>'xml_cdr_statistics.php?showall=true'.$search_url]);
	}
	echo button::create(['type'=>'button','label'=>$text['button-extension_summary'],'icon'=>'list','link'=>'xml_cdr_extension_summary.php']);
	echo button::create(['type'=>'button','label'=>$text['button-download_csv'],'icon'=>$settings->get('theme', 'button_icon_download'),'link'=>'xml_cdr_statistics_csv.php?type=csv'.$search_url]);
	echo "	</div>\n";
	echo "	<div style='clear: both;'></div>\n";
	echo "</div>\n";

	echo $text['label-call-statistics-description']."\n";
	echo "<br /><br />\n";

	echo "<div class='card'>\n";
	?>
	<script src='/resources/chartjs/chart.min.js'></script>
	<script src='/resources/chartjs/chartjs-adapter-date-fns.bundle.min.js'></script>

	<div align='center' style="justify-content: center; margin-bottom: 25px;">
		<div style="max-width: 100%; width: 800px; height: 280px;">
			<canvas id="cdr_stats_chart" style="width: 100%; height: 100%;"></canvas>
		</div>
	</div>

	<script type="text/javascript">
		var ctx = document.getElementById("cdr_stats_chart").getContext('2d');

		const cdr_stats_data = {
			datasets: [
				{
					label: "Volume",
					data: <?php echo json_encode($graph['volume']); ?>,
					backgroundColor: "#EDC240",
					borderColor: "#EDC240",
					fill: false
				},
				{
					label: "Minutes",
					data: <?php echo json_encode($graph['minutes']); ?>,
					backgroundColor: "#AFD8F8",
					borderColor: "#AFD8F8",
					fill: false
				},
				{
					label: "Calls Per Min",
					data: <?php echo json_encode($graph['call_per_min']); ?>,
					backgroundColor: "#CB4B4B",
					borderColor: "#CB4B4B",
					fill: false
				},
				{
					label: "Missed",
					data: <?php echo json_encode($graph['missed']); ?>,
					backgroundColor: "#4DA74D",
					borderColor: "#4DA74D",
					fill: false
				},
				{
					label: "ASR",
					data: <?php echo json_encode($graph['asr']); ?>,
					backgroundColor: "#9440ED",
					borderColor: "#9440ED",
					fill: false
				},
				{
					label: "ALOC",
					data: <?php echo json_encode($graph['aloc']); ?>,
					backgroundColor: "#BD9B33",
					borderColor: "#BD9B33",
					fill: false
				}
			]
		};

		const cdr_stats_config = {
			type: 'line',
			data: cdr_stats_data,
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: true,
						labels: {
							usePointStyle: true,
							pointStyle: 'rect',
							color: '#444',
							boxWidth: 15
						}
					}
				},
				scales: {
					x: {
						type: "time",
					},
					y: {
						min: 0
					}
				},
				elements: {
					line: {
						tension: 0.3
					}
				}
			},
			scales: {
				<?php
				if ($hours <= 48) {
					echo "xAxes: {type: \"time\",timeFormat: \"%d:%H\",minTickSize: [1, \"hour\"]}";
				}
				else if ($hours > 48 && $hours < 168) {
					echo "xAxes: {type: \"time\",timeFormat: \"%m:%d\",minTickSize: [1, \"day\"]}";
				}
				else {
					echo "xAxes: {type: \"time\",timeFormat: \"%m:%d\",minTickSize: [1, \"month\"]}";
				}
				?>,
				yAxes: [{
					ticks: {
						beginAtZero: true
					}
				}]
			}
		};

		const cdr_stats_chart = new Chart(ctx, cdr_stats_config);
	</script>

	<?php
	echo "</div>\n";

//show the results
	echo "<div class='card'>\n";
	echo "<table class='list'>\n";
	echo "<tr class='list-header'>\n";
	echo "	<th>".$text['label-hours']."</th>\n";
	echo "	<th>".$text['label-date']."</th>\n";
	echo "	<th class='no-wrap'>".$text['label-time']."</th>\n";
	echo "	<th title='".$text['description-volume']."'>".$text['label-volume']."</th>\n";
	echo "	<th>".$text['label-minutes']."</th>\n";
	echo "	<th title='".$text['description-calls-per-minute']."'>".$text['label-calls-per-minute']."</th>\n";
	echo "	<th class='center'>".$text['label-missed']."</th>\n";
	echo "	<th title='".$text['description-asr']."'>".$text['label-asr']."</th>\n";
	echo "	<th title='".$text['description-aloc']."'>".$text['label-aloc']."</th>\n";
	echo "</tr>\n";

	$i = 0;
	foreach ($stats as $row) {
		echo "<tr class='list-row'>\n";
		if ($i <= $hours) {
			echo "	<td>".$row['hours']."</td>\n";
		}
		else if ($i == $hours+1) {
			echo "	<br /><br />\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td>\n";
			echo "		<br /><br />\n";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr class='list-header'>\n";
			echo "	<th class='no-wrap'>".$text['label-days']."</th>\n";
			echo "	<th class='no-wrap'>".$text['label-date']."</th>\n";
			echo "	<th class='no-wrap'>".$text['label-time']."</th>\n";
			echo "	<th>Volume</th>\n";
			echo "	<th>".$text['label-minutes']."</th>\n";
			echo "	<th class='no-wrap'>".$text['label-calls-per-minute']."</th>\n";
			echo "	<th class='center'>".$text['label-missed']."</th>\n";
			echo "	<th>ASR</th>\n";
			echo "	<th>ALOC</th>\n";
			echo "</tr>\n";
			echo "<tr class='list-row'>\n";
		}
		if ($i > $hours) {
			echo "	<td>" . floor(escape($row['s_hour'])/24) . "</td>\n";
		}
		if ($i <= $hours) {
			echo "	<td>".$row['date']."</td>\n";
			echo "	<td>".$row['time']."&nbsp;</td>\n";
		}
		else {
			echo "	<td>".$row['date']."</td>\n";
			echo "	<td>".$row['time']."&nbsp;</td>\n";
		}
		echo "	<td>".escape($row['volume'])."&nbsp;</td>\n";
		echo "	<td>".escape(round($row['minutes'] ?? 0, 2))."&nbsp;</td>\n";
		echo "	<td>".escape(round($row['avg_min'] ?? 0, 2))."&nbsp;/&nbsp;".escape(round($row['cpm_answered'] ?? 0, 2))."&nbsp;</td>\n";
		echo "	<td class='center'><a href=\"xml_cdr.php?call_result=missed&direction=".$direction."&start_epoch=".escape($row['start_epoch'] ?? '')."&stop_epoch=".escape($row['stop_epoch'] ?? '')."\">".escape($row['missed'] ?? '')."</a>&nbsp;</td>\n";
		echo "	<td>".escape(round($row['asr'] ?? 0, 2))."&nbsp;</td>\n";
		echo "	<td>".escape(round($row['aloc'] ?? 0, 2))."&nbsp;</td>\n";
		echo "</tr >\n";
		$i++;
	}
	echo "</table>\n";
	echo "</div>\n";
	echo "<br><br>";

//include the footer
	require_once "resources/footer.php";

?>
