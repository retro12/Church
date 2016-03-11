{% extends 'templates/default.php' %}

{% block title %} Gallery {% endblock %}

{% block content %}
	<div class="row">
		<div class="col-md-12 col-md-offset-">
			<div class="panel panel-default">
				<div class="panel-heading text-center">
					<div class="back pull-left">
						<a href="{{ urlFor('home') }}" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-menu-left"></i> Back</a>
					</div>
					<h3 class="panel-title">Gallery</h3>
				</div>
				<div class="panel-body">
					{% for album in albums %}
						<div class="col-xs-6 col-md-3">
							<div class="album">
								<a href="{{ urlFor('albums.album_photos', {'id': album.id}) }}">
									<!--Provjera da li ne postoji slika u albumu i onda prikazivanje defaultne slike albuma-->
									<img src="{{ not album.getAlbumThumbnail.path ? album.getAlbumThumbnail : album.getAlbumThumbnail.path }}" alt="Album thumbnail.jpg" class="album__thumb">
								</a>
							</div>
							<a href="{{ urlFor('albums.album_photos', {'id': album.id}) }}" class="btn btn-primary album__title">{{ album.title[:14] }} <span class="badge">{{ album.countPhotosInAlbum }} photos</span></a>
						</div>
					{% endfor %}
				</div>
			</div>
		</div>
	</div>
{% endblock %}