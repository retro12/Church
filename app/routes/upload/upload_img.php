<?php

require_once '../vendor/samayo/bulletproof/src/utils/func.image-resize.php'; //Ukljucivanje funkcije za smanjivanje resize slike

//Get URL putanja

$app->get('/upload', function() use ($app) {
	$app->render('/upload/upload_img.php');
})->name('upload');


//Post putanja za obradu podataka iz forme

$app->post('/upload', function() use ($app) {

	//Dohvatanje Slim request objekata
	$request = $app->request;

	$img_title = $request->post('img_title');
	$picture = $_FILES['picture'];

	//Provjera da li postoji nesto u $_FILES globalnoj var. tj da li je slika poslata
	//echo (!empty($_FILES['picture'])) ? "No files uploaded!!" : 'ok';

	/*
		if (empty($_FILES['picture']['name']))
		{
	        $app->flash('global', 'Please select pictures files for upload!');
	        return $app->response->redirect($app->urlFor('upload'));
	    }
	*/

	//Pozivanje validacijske klase
	$v = $app->validation;

	//Validacija polja iz forme
	$v->validate([
		'img_title' => [$img_title, 'required|alnumDash|min(4)'],
		'picture' => [$_FILES['picture']['name'], 'required']
	]);
	
	//Ako je validacija prosla uspijesno

	if ($v->passes())
	{
		$image = $app->image; //Dohvatanje Image klase sa start.php fajla iz Slim2 containera

		$allowedMIME = ['jpg','jpeg','png']; //Dozvoljeni niz ekstenzija za upload

		//Nmjestanje dozvoljenog niza estenzija,dozvoljene velicine fajla,dozvoljene dizmenzije slike,i smijestanje u profile_img folder.
		$image->setMime($allowedMIME)->setSize(1000, 1048576)->setDimension(500, 500)->setLocation(INC_ROOT . '\app\uploads\profile_img');

		//Provjera da li uplodovana slika postoji
		if($image['picture'])
		{
			$upload = $image->upload(); 

			//Provjera da li je slika ucitana na zeljenu lokaciju
			if($upload)
			{
				//Smanjivanje slike na zeljenu velicinu
				$resize = Bulletproof\resize (
							$image->getFullPath(), 
							$image->getMime(),
							$image->getWidth(),
							$image->getHeight(),
							100,
							100
				);

				//Dohvatanje putanje do slike i imena slike
				$img_path = $app->config->get('app.url') . '/Vijezbe/Church/app/uploads/profile_img/' . $image->getName() . '.' . $image->getMime();

				$user = $app->auth; //Cuvanje korisnikovih podataka u var.

				//Update korisnikov red u bazi p. sa putanjom do profil slike i naslovom slike

				$user->update([
					'img_path' => $img_path,
					'img_title' => $img_title
				]);

				//Ispis potvrdne poruke i redirekcija na upload_img.php stranicu
				$app->flash('global', 'Your profile picture is uploaded sucessfully.');
				$app->response->redirect($app->urlFor('upload'));
			}
			else
			{	
				//Ispis greske i redirekcija na upload_img.php stranicu
				$app->flash('global', $image['error']);
				$app->response->redirect($app->urlFor('upload'));
			}
		}
	}

	$app->render('/upload/upload_img.php', [
		'errors' => $v->errors(),
		'request' => $request
	]);

})->name('upload.post');

?>