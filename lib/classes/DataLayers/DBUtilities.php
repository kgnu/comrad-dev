<?php

################################################################################
# OBJECT:       DBUtilities                                                    #
# AUTHOR:       Tom Buzbee (02/23/2010)                                        #
# DESCRIPTION:  Provides some convenience methods for accessing the database   #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/02/23 (TB) - Created
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

class DBUtilities
{
	////////////////////////////////////////////////////////////////////////////
	// Returns a list of all the genre names in an array
	public static function getGenreNames()
	{
		// Get the first 512
		$catalog = DB::getInstance('MySql');
		$genreObjects = $catalog->find(new Genre(), $count, array("sortorder" => "GenreID", "limit" => 512));
		
		// Append each Genre's Name
		foreach ($genreObjects as $object)
		{
			$genres[$object->GenreID] = $object->Name;
		}
		
		// TODO - Do another query if $count > 512

		return $genres;
	}
	
	////////////////////////////////////////////////////////////////////////////
	// Cross-checks a supplied list of genres with existing GenreTags and updates accordingly
	public static function updateGenreTags($album, $genres)
	{
		$catalog = DB::getInstance('MySql');
		$genreNames = DBUtilities::getGenreNames();
		$genreIDs = array_flip($genreNames);
		$newGenreIDs = array_flip($genres);
		
		// Retrieve existing GenreTags for this album
		$existingTags = $catalog->find(new GenreTag(array(AlbumID => $album->AlbumID)));
		
		// Delete missing tags
		$existingGenreNames = array();
		foreach ($existingTags as $tag)
		{
			if (!array_key_exists($genreNames[$tag->GenreID], $newGenreIDs))
			{
				$catalog->delete($tag);
			}
			
			// Make a list to expedite adding new tags
			$existingGenreNames[$genreNames[$tag->GenreID]] = true;
		}
		
		// Add new tags
		foreach ($genres as $genre)
		{
			if (!array_key_exists($genre, $existingGenreNames) && array_key_exists($genre, $genreIDs))
			{
				$catalog->insert(new GenreTag(array(
					AlbumID => $album->AlbumID,
					GenreID => $genreIDs[$genre]
				)));
			}
		}
		
		return true;
	}
}

?>
