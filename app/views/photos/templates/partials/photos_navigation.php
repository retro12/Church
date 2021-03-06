<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading text-center">
				<div class="back pull-left">
					<a href="{{ urlFor('home') }}" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-menu-left"></i> Back</a>
				</div>
				<h3 class="panel-title">Photos Options</h3>
			</div>
			<div class="panel-body">
				<div class="list-group">
					<a href="{{ urlFor('albums.create_album') }}" class="list-group-item text-center">Create Album</a>
					<a href="{{ urlFor('upload.photos') }}" class="list-group-item text-center">Add Photos</a>
					<a href="{{ urlFor('photos.all_photos', {'id': 1}) }}" class="list-group-item text-center">Your Photos</a>
					<a href="{{ urlFor('albums.all_albums', {'id': 1, 'gid': gid, 'aid': aid}) }}" class="list-group-item text-center">Albums</a>
				</div>
			</div>
		</div>
	</div>
</div>