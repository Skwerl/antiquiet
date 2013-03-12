<?php

function is_mcdonalds_festivalfever() {

	$festivalfever_tags = array(
		'SXSW',
		'Coachella',
		'Mountain Jam',
		'Bonnaroo',
		'FestivalFever'
		);
	$festivalfever_artists = array(
		'Baroness',
		'Bat For Lashes',
		'Biffy Clyro',
		'Big Black Delta',
		'Bjšrk',
		'Black Moth Super Rainbow',
		'Blur',
		'Cat Power',
		'Cold War Kids',
		'Death Grips',
		'Depeche Mode',
		'Dessa',
		'Dirty Ghosts',
		'Edward Sharpe & The Magnetic Zeros',
		'El-P',
		'Gary Clark Jr.',
		'Ghost',
		'Grinderman',
		'How To Destroy Angels',
		'Janelle Mon‡e',
		'Japandroids',
		'Jeff The Brotherhood',
		'Jurassic 5',
		'Kid Koala',
		'Lou Reed',
		'Macklemore',
		'Meat Puppets',
		'Nas',
		'Nick Cave And The Bad Seeds',
		'Paul McCartney',
		'Phil Lesh And Friends',
		'Phoenix',
		'P.O.S.',
		'Portugal. The Man',
		'Primus',
		'Puscifer',
		'Ra Ra Riot',
		'Red Hot Chili Peppers',
		'Reignwolf',
		'Sigur R—s',
		'Stone Roses',
		'Surfer Blood',
		'Tegan And Sara',
		'The Avett Brothers',
		'The Crystal Method',
		'The National',
		'The Postal Service',
		'The XX',
		'Tom Petty',
		'Unknown Mortal Orchestra',
		'Wavves',
		'Weird Al Yankovic',
		'Yeah Yeah Yeahs'
		);

	global $post;

	if (in_category('festivals')) {
		return true;
	}
	
	if (has_tag($festivalfever_tags)) {
		return true;
	}
	
	$artist_tags = get_the_terms($post->ID, 'artist');
	foreach ($artist_tags as $artist_tag) {
		if (in_array($artist_tag->name,$festivalfever_artists)) {
			return true;
		}
	}

	return false;

}

?>

<?php if (is_mcdonalds_festivalfever()) { ?>
	<script type="text/javascript">
		GA_googleAddAttr("McFests", "1");
	</script>
<?php } ?>