<?php

//Ucitavanje slika u albume i galeriju

//Get putanja

$app->get('/upload_photos', $authenticated(), function () use ($app) {

	//Dohvatanje svih korisnikovih albuma  koje je kreirao i slanje na views da se iskoristi za dropdovn listu
	$albums = $app->album->getUserAlbums($app->auth->id);

	//Dohvatanje st. iz viewsa
	return $app->render('upload/upload_photos.php', [
		'albums' => $albums
	]);

})->name('upload.photos');

//Post putanja za obradu podataka iz forme

$app->post('/upload_photos', $authenticated(), function () use ($app) {

	$app->response()->header('Content-Type', 'application/json'); //Namjestanje headera

	//Dohvatanje Photos klase
	$photo = $app->photo;

	//Request objekat
	$request = $app->request;

	//Kupljenje podataka iz forme
	$albumId = $request->post('albums');
	$photos = $_FILES['photos']['name'];
	$size = $_FILES['photos']['size'];
	$type = $_FILES['photos']['type'];

	//Dohvatanje validacijske klase
	$v = $app->validation;

	//Provjera da li je validacija prosla uspijesno

	if ($v->validate([
		'albums' => [$albumId, 'required'],
		'photos' => [$_FILES['photos']['name'], 'required']
	]));
	
	//Dohvatanje imena albuma,radi ucitavanje slika u njega
	$albumName = $app->album->where('id', $albumId)->select('title')->first();

	//Direktorijum za ucitavanje slika
	$uploadDir = INC_ROOT . "/app/uploads/gallery/{$albumName->title}/";

	//Ako je validacija prosla uspijesno

	if ($v->passes())
	{
		//Nova instanca fUpload klase
		$uploader = $app->fupload;

		//Setiranje dozvoljenih ekstenzija
		$uploader->setMIMETypes(array(
			'image/jpg',
			'image/jpeg',
			'image/png',
			'image/gif',
		), 'The file uploaded in not an allowed image type.');

		//Ogranicavanje velicine fajla
		$uploader->setMaxSize('5MB');

		//Validacija ucitanog fajla
		$error = $uploader->validate('photos', true);

		//Folder za smijestanje slika
		$dir = new fDirectory($uploadDir);

		//Var. u kojoj cuvamo ucitane slike u obliku niza
		$files = array();

		//Prebrojavanje ucitanih slika
		$uploaded = fUpload::count('photos');

		//Pokrecmo for petlju da izlista i ucita sve slike jednu po jednu
		for ($i = 0; $i < $uploaded; $i++)
		{	
			//Prolaz kroz sve ucitane slike i ucitavanje jedne po jedne u odredjeni folder
		    $files[] = $uploader->move($dir, 'photos', $i);

		    //Promjena apsolutne putanje u http:// putanju slike
			$path = str_replace(dirname(dirname(INC_ROOT)), $app->config->get('app.url'), $uploadDir . strtolower($photos[$i]));

		    //Upis svih slika u bazu podataka
		    $photo->create([
		    	'user_id' => $app->auth->id,
		    	'album_id' => $albumId,
				'path' => $path,
		    	'size' => $size[$i],
		    	'type' => $type[$i]
		    ]);
		}

		//Odgovor server o uspijesnom ili ne uspijesnom uploadu fajlova i foldera
		if ($uploaded)
		{
			echo json_encode(array(
				"status" => true,
				"message" => "Photos are successfully uploaded.",
			));
		}
		else
		{
			echo json_encode(array(
				"status" => false,
				"message" => $v->errors(),
			));
		}

		//Redirekcija na upload st. sa porukom
		//$app->flash('global', 'Your photos has been uploaded.');
		//return $app->response->redirect($app->urlFor('upload.photos'));
	}
	
	//Zakomentarisano zato sto saljemo podtake uz pomoc AJAX-a
	//Dohvatanje st. iz viewsa
	/*return $app->render('upload/upload_photos.php', [
		'errors' => $v->errors(),
		'uploadFolder' => $dir
	]);*/

})->name('upload.photos.post');

?>