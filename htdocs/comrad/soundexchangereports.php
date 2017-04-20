<?php

	require_once('initialize.php'); 
	
	if (isset($_POST['submit'])) {
		$dateFrom = $_POST['dateFrom'];
		$dateTo = $_POST['dateTo'];
		if (empty($dateFrom) && empty($dateTo)) exit(); //we must have a dateFrom or dateTo value
		
		require('phpexcel/PHPExcel.php');
		
		//run this query:
		$trackPlayManager = TrackPlayManager::getInstance();
		
		$db = new mysqli(
			$init->getProp('MySql_Host'),
			$init->getProp('MySql_Username'),
			$init->getProp('MySql_Password'),
			$init->getProp('MySql_Database')
		);
		
		$query = "SELECT sei.sei_StartDateTime AS EventStartDateTime, sei.sei_Duration AS EventDuration,
						 t.t_Duration AS TrackDuration, t.t_Title AS TrackTitle, t.t_Artist AS TrackArtist, 
						 a.a_Artist AS AlbumArtist, a.a_Title AS AlbumTitle, a.a_Label AS AlbumLabel,
						 e.e_Title AS ShowName, h.Name AS HostName
			FROM FloatingShowElement AS fse
			INNER JOIN ScheduledEventInstance AS sei ON sei.sei_Id = fse.fse_ScheduledShowInstanceId
			INNER JOIN ScheduledEvent AS se ON se.se_Id = sei.sei_ScheduledEventId
			INNER JOIN Event AS e ON e.e_Id = se.se_EventId
			INNER JOIN Tracks AS t ON t.t_TrackID = fse.fse_TrackId
			LEFT JOIN Albums AS a ON t.t_AlbumID = a.a_AlbumID
			LEFT JOIN Host as h ON h.UID = sei.sei_HostId
			WHERE fse.fse_DISCRIMINATOR = 'TrackPlay' AND " .
			(!empty($dateFrom) ? "sei.sei_StartDateTime >= '" . $db->real_escape_string(date('Y-m-d', strtotime($dateFrom))) . " 00:00:00' " : '') .
			(!empty($dateFrom) && !empty($dateTo) ? 'AND ' : '') .
			(!empty($dateTo) ? "sei.sei_StartDateTime < DATE_ADD('" . $db->real_escape_string(date('Y-m-d', strtotime($dateTo))) . " 00:00:00', INTERVAL 1 DAY) " : '') .
			" ORDER BY fse.fse_StartDateTime";
		
		$result = $db->query($query);
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		if (!empty($dateFrom) && !empty($dateTo)) {
			$title = "Comrad Sound Exchange Report " . date('m-d-Y', strtotime($dateFrom)) . ' to ' . date('m-d-Y', strtotime($dateTo));
		} else if (!empty($dateFrom)) {
			$title = "Comrad Sound Exchange Report " . date('m-d-Y', strtotime($dateFrom)) . " to " . date('m-d-Y');
		} else {
			$title = "Comrad Sound Exchange Report before " . date('m-d-Y', strtotime($dateTo));
		}
		$objPHPExcel->getProperties()->setCreator("Comrad")
									 ->setLastModifiedBy("Comrad")
									 ->setTitle($title)
									 ->setSubject($title)
									 ->setDescription("Comrad Sound Exchange Report");
		
		$genre = NULL;
		$lineNo = 2;
		$unknownGenreMaxLineNumber = 0;
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('Sound Exchange Report');
		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', '#')
					->setCellValue('B1', 'Start time')
					->setCellValue('C1', 'End time')
					->setCellValue('D1', 'Duration (in seconds)')
					->setCellValue('E1', 'Track title')
					->setCellValue('F1', 'Artist')
					->setCellValue('G1', 'Album Title')
					->setCellValue('H1', 'Label')
					->setCellValue('I1', 'Show ID')
					->setCellValue('J1', 'Show')
					->setCellValue('K1', 'Host');
		$objPHPExcel->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(TRUE);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(28);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(22);
		
		$i = 1;
		$showId = 0;
		$startTime;
		while ($line = $result->fetch_assoc()) {
			if ($startTime != $line['EventStartDateTime']) { //new show, increment show ID
				$showId++;
				if ($showId > 1) {
					//fill in the end time for the previous show
					$objPHPExcel->getActiveSheet()->setCellValue('C' . ($i), date('m-d-Y g:ia', $endTime));
				}
			}
			$objPHPExcel->getActiveSheet()
						->setCellValue('A' . ($i + 1), $i)
						->setCellValue('B' . ($i + 1), ($startTime != $line['EventStartDateTime'] ? date('m-d-Y g:ia', strtotime($line['EventStartDateTime'])) : ''))
						//skip row C - end time
						->setCellValue('D' . ($i + 1), $line['TrackDuration'])
						->setCellValue('E' . ($i + 1), $line['TrackTitle'])
						->setCellValue('F' . ($i + 1), !empty($line['TrackArtist']) ? $line['TrackArtist'] : $line['AlbumArtist'])
						->setCellValue('G' . ($i + 1), $line['AlbumTitle'])
						->setCellValue('H' . ($i + 1), $line['AlbumLabel'])
						->setCellValue('I' . ($i + 1), $showId)
						->setCellValue('J' . ($i + 1), $line['ShowName'])
						->setCellValue('K' . ($i + 1), $line['HostName']);
			$startTime = $line['EventStartDateTime'];
			$endTime = strtotime($startTime) + $line['EventDuration'] * 60;
			$i++;
		}
		
		//fill in the end time for the last show
		$objPHPExcel->getActiveSheet()->setCellValue('C' . ($i), date('m-d-Y g:ia', $endTime));
		
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="charting_' . 
				(!empty($dateFrom) ? str_replace('/', '-', $dateFrom) . '_' : '') . 
				(!empty($dateTo) ? str_replace('/', '-', $dateTo) : date('m-d-Y')) . 
				'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		
		$result->free();
		$db->close();
		
		exit;
	}
	
?>

<?php ###################################################################### ?>
<?php $head=new HeadTemplateSection();                                     # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Charting</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/jgrowl/jquery.jgrowl.css" />
	<style type="text/css">
		label { display: block; font-size: 1.2em; font-weight: bold }
		.field { width: 400px; margin: 35px 30px }
		.inputField { width: 100% }
		.required { color: #c33 }
		form p { margin: 0px; padding: 0px }
	</style>
	
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/jquery/jgrowl/jquery.jgrowl.js"></script>

	<script type="text/javascript">
		
		$(function() {
			$("input[type='text']").datepicker({dateFormat: 'm/d/yy' });
			
			$("form").submit(function(e) {
				//validation
				var valid = false;
				$("input[type='text']").each(function() {
					if ($.trim($(this).val()).length > 0) {
						valid = true;
					}
				});
				if ( ! valid) {
					$.jGrowl('Please fill out at least one field.', {
						header: 'Error',
						life: 10000,
						glue: 'before'
					});
					e.preventDefault();
				}
				
				
			});
		});
	
	</script>

<?php ###################################################################### ?>
<?php $body=new BodyTemplateSection();                                     # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<h4>Sound Exchange Reports</h4>
	
	<fieldset>
		<p>
			Please fill out at least one date field. If you leave the "From" field blank, the report will go back to the oldest track plays. If you leave the "To" field blank, the report will go up to the current track plays.
		</p>
		
		<form id="SoundExchangeReportsForm" action="soundexchangereports.php" method="post">
			
			<div class="field">
				<label for="name">From</label>
				<input type="text" class="inputField" id="dateFrom" name="dateFrom" />
			</div>
			<div class="field">
				<label for="name">To</label>
				<input type="text" class="inputField" id="dateTo" name="dateTo" />
			</div>
			<input type="submit" name="submit" value="Submit" id="saveButton">
		
		</form>
		
	</fieldset>
		
	
<?php ###################################################################### ?>
<?php $close=new CloseTemplateSection();                                   # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
