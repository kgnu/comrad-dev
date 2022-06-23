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
		
		$query = 'SELECT Count(*) as Plays, AlbumTitle, GROUP_CONCAT(DISTINCT TrackArtist ORDER BY TrackArtist SEPARATOR \',\') AS TrackArtist, Artist, AddDate, AlbumId, Label, Genre FROM SoundExchangePlaylist ' .
				 'WHERE ' .
				 (!empty($dateFrom) ? "StartDateTime >= '" . $db->real_escape_string(date('Y-m-d', strtotime($dateFrom))) . " 00:00:00' " : '') .
				 (!empty($dateFrom) && !empty($dateTo) ? 'AND ' : '') .
				 (!empty($dateTo) ? "StartDateTime < DATE_ADD('" . $db->real_escape_string(date('Y-m-d', strtotime($dateTo))) . " 00:00:00', INTERVAL 1 DAY) " : '') .
				 'GROUP BY AlbumId ' .
				 'ORDER BY Genre ASC, Plays DESC';
		
		$result = $db->query($query);
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		if (!empty($dateFrom) && !empty($dateTo)) {
			$title = "Comrad Charting Report " . date('m-d-Y', strtotime($dateFrom)) . ' to ' . date('m-d-Y', strtotime($dateTo));
		} else if (!empty($dateFrom)) {
			$title = "Comrad Charting Report " . date('m-d-Y', strtotime($dateFrom)) . " to " . date('m-d-Y');
		} else {
			$title = "Comrad Charting Report before " . date('m-d-Y', strtotime($dateTo));
		}
		$objPHPExcel->getProperties()->setCreator("Comrad")
									 ->setLastModifiedBy("Comrad")
									 ->setTitle($title)
									 ->setSubject($title)
									 ->setDescription("Comrad Charting Report");
		
		$genre = NULL;
		$activeSheet = 0;
		$lineNo = 2;
		$unknownGenreMaxLineNumber = 0;
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('Unknown genre');
		setSheetTitles($objPHPExcel);
		
		while ($line = $result->fetch_assoc()) {
			if ($genre != $line['Genre']) {
				if ($line['Genre'] == 'Unknown') {
					$objPHPExcel->setActiveSheetIndex(0);
					$lineNo = $unknownGenreMaxLineNumber;
				} else {
					$activeSheet++;
					$objPHPExcel->createSheet($activeSheet);
					$objPHPExcel->setActiveSheetIndex($activeSheet);
					$objPHPExcel->getActiveSheet()->setTitle(str_replace('/', ' ', $line['Genre']));
					$lineNo = 2;
					setSheetTitles($objPHPExcel);
				}
				$genre = $line['Genre'];
			}
			// Add some data
			$objPHPExcel->getActiveSheet()
						->setCellValue('A' . $lineNo, $line['Plays'])
						->setCellValue('B' . $lineNo, !empty($line['TrackArtist']) ? $line['TrackArtist'] : $line['Artist'])
						->setCellValue('C' . $lineNo, $line['AlbumId'])
						->setCellValue('D' . $lineNo, $line['AlbumTitle'])
						->setCellValue('E' . $lineNo, $line['Label'])
						->setCellValue('F' . $lineNo, $line['AddDate'])
						->setCellValue('G' . $lineNo, $line['Genre']);
			$lineNo++;
			if ($genre == NULL) {
				$unknownGenreMaxLineNumber = $lineNo;
			}
		}
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
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
	
	function setSheetTitles(&$objPHPExcel) {
		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', 'Plays')
					->setCellValue('B1', 'Artist')
					->setCellValue('C1', 'Album ID')
					->setCellValue('D1', 'Album Title')
					->setCellValue('E1', 'Label')
					->setCellValue('F1', 'Add Date')
					->setCellValue('G1', 'Genre');
		$objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold(TRUE);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(28);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(26);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(21);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
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

	<h4>Charting</h4>
	
	<fieldset>
		<p>
			Please fill out at least one date field. If you leave the "From" field blank, the report will go back to the oldest track plays. If you leave the "To" field blank, the report will go up to the current track plays.
		</p>
		
		<form id="ChartingForm" action="charting.php" method="post">
			
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
