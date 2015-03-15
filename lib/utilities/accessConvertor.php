#!/usr/bin/php
<?php
	class accessTranslator
	{
		private $accessLink;
		private $catalog;
		private $CDCodeMapping;
		private $GenreIDMapping;

		public function __construct($accessDBName, $host, $username, $password, $accessSQLDump = "")
		{
			$this->catalog = DB::getInstance('MySql');
			
			mysqli_report(MYSQLI_REPORT_ERROR);
			$this->accessLink = new mysqli($host, $username, $password);
			if ($this->accessLink->connect_error)
			{
    				die('Connect Error (' . $this->accessLink->connect_errno . ') '. $this->accessLink->connect_error);
			}
			$this->accessLink->select_db($accessDBName);
		}
		
		public function __destruct()
		{
			$this->accessLink->close();
		}
		
		public function translate()
		{
			$this->translateGenre();
			$this->translateAlbum();
			$this->translateTracks();
		}
		
		private function translateGenre()
		{
			$GenreID = $this->catalog->insert(new Genre(array(
				'Name' => 'Unknown',
				'TopLevel' => true
			)));
			$this->GenreIDMapping['Unknown'] = $GenreID;
					
			// Main Genres
			if($result = $this->accessLink->query("SELECT * FROM T_Genre", MYSQLI_USE_RESULT))
			{
				while($row = $result->fetch_assoc())
				{
					$name = ucwords(trim($row[Description]));
					if ($name)
					{
						echo "Genre: $name\n";
						$GenreID = $this->catalog->insert(new Genre(array(
							'Name' => $name,
							'TopLevel' => true
						)));
						$this->GenreIDMapping[$name] = $GenreID;
					}
				}
				$result->close();
			}
			
			// Sub-genres
			if($result = $this->accessLink->query("SELECT * FROM T_Sub_Genre", MYSQLI_USE_RESULT))
			{
				while($row = $result->fetch_assoc())
				{
					$name = ucwords(trim($row[Description]));
					if ($name && !array_key_exists($name, $this->GenreIDMapping))
					{
						// echo "Genre: $name\n";
						$GenreID = $this->catalog->insert(new Genre(array(
							'Name' => $name,
							'TopLevel' => false
						)));
						$this->GenreIDMapping[$name] = $GenreID;
					}
				}
				$result->close();
			}
			
			// Sub-sub-genres
			if($result = $this->accessLink->query("SELECT * FROM T_Sub_Sub_Genre", MYSQLI_USE_RESULT))
			{
				while($row = $result->fetch_assoc())
				{
					$name = ucwords(trim($row[Description]));
					if ($name && !array_key_exists($name, $this->GenreIDMapping))
					{
						// echo "Genre: $name\n";
						$GenreID = $this->catalog->insert(new Genre(array(
							'Name' => $name,
							'TopLevel' => false
						)));
						$this->GenreIDMapping[$name] = $GenreID;
					}
				}
				$result->close();
			}
			
			echo "Genres done...\n\n";
		}
		
		private function translateAlbum()
		{
			$result = $this->accessLink->query("SELECT * FROM T_Temp_Tallys_Long LIMIT 500", MYSQLI_STORE_RESULT);
			$i = 0;
			while ($result && $result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{					
					if(empty($row[Artist]) || empty($row[Title]))
					{
						// echo "Skipping Album with null Artist/Title\n";
						continue;
					}
					if(empty($row[COMPANY]))
					{
						$row[COMPANY] = "Unknown";
					}
					if($row[ID] < 10000 || $row[ID] > 999999)
					{
						// echo "Skipping Album with out-of-bounds code: $row[ID]\n";
						continue;
					}
					
					if (array_key_exists(ucwords(trim($row[Description])), $this->GenreIDMapping))
						$genreNum = $this->GenreIDMapping[ucwords(trim($row[Description]))];
					else
						$genreNum = $this->GenreIDMapping['Unknown'];
					
					$album = new Album(array(
						'Title' => $row[Title],
						'Label' => $row[COMPANY],
						'GenreID' => $genreNum,
						'AddDate' => $row[Received],
						'Artist' => $row[Artist],
						'CDCode' => $row[ID],
						'Location' => 'Library'
					));
					if($AlbumID = $this->catalog->insert($album))
					{
						// echo "Album: $row[ID], $row[Title], $row[Artist]\n";	
						$this->CDCodeMapping[$row[ID]] = $AlbumID;
					}
				}
				$result->close();
				
				$i += 500;
				$result = $this->accessLink->query("SELECT * FROM T_Temp_Tallys_Long LIMIT $i,500", MYSQLI_STORE_RESULT);
				echo "$i albums processed...\n";
			}
			
			echo "Albums done...\n\n";
		}

		private function translateTracks()
		{
			$result = $this->accessLink->query("SELECT * FROM T_Track LIMIT 500", MYSQLI_STORE_RESULT);
			$i = 0;
			while ($result && $result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{	
					if(empty($row[Record_ID]) || empty($row[Name]))
					{
						// echo "Skipping Track with null Record_ID/Title\n";
						continue;
					}
					
					if(!array_key_exists($row[Record_ID], $this->CDCodeMapping))
					{
						// echo "Skipping Track with unknown CDCode: $row[Record_ID]\n";
						continue;
					}
					$AlbumID = $this->CDCodeMapping[$row[Record_ID]];
					
					// echo "Track: $row[Name], CD: $row[Record_ID]\n";	
					$track = new Track(array(
						'AlbumID' => $AlbumID,
						'Title' => $row[Name],
						'TrackNumber' => 1,
						'DiskNumber' => 1,
						'Duration' => 0,
					));
					$this->catalog->insert($track);
				}
				$result->close();
				
				$i += 500;
				$result = $this->accessLink->query("SELECT * FROM T_Track LIMIT $i,500", MYSQLI_STORE_RESULT);
				echo "$i tracks processed...\n";
			}
			
			echo "Tracks done...\n\n";
		}
	}

	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();
	
	$aT = new accessTranslator("movedb", $init->getProp('Catalog_Host'), $init->getProp('Catalog_Username'), $init->getProp('Catalog_Password'));
	$aT->translate();
?>
