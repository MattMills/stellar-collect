<?php
	$this_page = 'collect';
	require_once('../stellar-web/header.php');
?>
	<div id="checklist" class="container-fluid" style="width:80%">
		<div class="row">
			<h2>StellarStellaris Mod & Crash detail collector</h2>
			<div class="alert alert-info"><h3>(STILL IN DEVELOPMENT)</h3></div>
		</div>
		<div class="row">This web app will look at your Stellaris data folder, and collect info on all your current mods, crashes and saved games.</div>
		<div class="row my-3">&nbsp;</div>
		<div class="row" id="native-file-api-err">
			<div class="col">
				<div class="alert alert-danger collapse">
					<h3>ERROR!</h3> Your browser doesn't support the native filesystem API so this app won't work. As of writing Edge, Chrome and Opera support it.
					<a href="https://caniuse.com/native-filesystem-api">Click here for up to date compatibility info</a>
				</div>
			</div>
		</div>
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<button class="nav-link active" id="load-tab" data-bs-toggle="tab" data-bs-target="#load-tab-pane" type="button" role="tab" aria-controls="load-tab-pane" aria-selected="true">Load</button>
			<li>
			<li class="nav-item">
				<button class="nav-link" id="mods-tab" data-bs-toggle="tab" data-bs-target="#mods-tab-pane" type="button" role="tab" aria-controls="mods-tab-pane" aria-selected="false">Mods</button>
			</li>
			<li class="nav-item">
				<button class="nav-link" id="crashes-tab" data-bs-toggle="tab" data-bs-target="#crashes-tab-pane" type="button" role="tab" aria-controls="crashes-tab-pane" aria-selected="false">Crashes</button>
			</li>
			<li class="nav-item">
				<button class="nav-link" id="saves-tab" data-bs-toggle="tab" data-bs-target="#saves-tab-pane" type="button" role="tab" aria-controls="saves-tab-pane" aria-selected="false">Save games</button>
			</li>
			<li class="nav-item ms-auto">
				<button class="nav-link" id="save-as-tab" data-bs-toggle="tab" data-bs-target="#save-as-tab-pane" type="button" role="tab" aria-controls="save-as-tab-pane" aria-selected="false"><i class="bi bi-link-45deg"></i>
 Save as Link</button>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane show active" id="load-tab-pane">
			<div class="row"><div class="col">&nbsp;</div>
			</div>
			<div class="row" id="select-folder">
				<div class="col-1">Step 1.</div>
				<div class="col"><button id="file-input-button" class="btn btn-primary">Browse to My Documents \ Paradox Interactive \ Stellaris folder</button></div>
				<div class="col-2" id="status"></div>
			</div>
			<div class="row collapse" id="select-folder-error">
				<div class="row">
					<div class="col-1"></div>
					<div class="col">
						<p class="alert-danger p-3" ><b>ERROR:</b> Unable to access Stellaris game_data.json on your PC, are you sure you selected the right folder?</p>
					</div>
					<div class="col-2"></div>
				</div>
			</div>
			<div class="row mt-2" id="link-steam-workshop">
				<div class="col-1">Step 2.</div>
				<div class="col">Checking for access to steam workshop</div>
				<div class="col-2" id="status"></div>
			</div>
			<div class="row my-2 collapse" id="link-steam-workshop-error">
				<div class="row">
					<div class="col-1"></div>
					<div class="col ml-3">
						<p class="alert-danger p-3" ><b>ERROR:</b> Unable to access Stellaris Steam workshop on your PC.</p>
					</div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col-1"></div>
					<div class="col">
						<p class=""> To enable access, open a command prompt (start->run->cmd) and enter these two commands:</p>
						<pre class="alert alert-dark"><code>cd "%USERPROFILE%\Documents\Paradox Interactive\Stellaris\"
mklink /J steam_workshop "%PROGRAMFILES(X86)%\Steam\steamapps\workshop\content\281990"</code></pre>
						<p>This will allow this page to access your Stellaris steam workshop using NTFS Junctions. 
						<i>Note, if your Steam workshop resides at a different location than the default, you may need to tweak the path.</i>
						</p>
						<p>Once you've done that, click <button id="link-steam-workshop-retry" class="btn btn-primary">Retry</button></p>
					</div>
					<div class="col-2"></div>
				</div>
			</div>
			<div class="row" id="discover-meta">
		                <div class="col-1">Step 3.</div>
		                <div class="col">Discover game metadata</div>
		                <div class="col-2" id="status"></div>
			</div>
			<div class="row" id="discover-mods">
				<div class="col-1">Step 4.</div>
				<div class="col">Discover enabled mods</div>
				<div class="col-2" id="status"></div>
			</div>
			<div class="row collapse" id="discover-mods-bug-warning">
				<div class="col alert-warning p-3 >
					<p><b>Known issue:</b> files with tildes (~) cannot be loaded due to <a href="https://bugs.chromium.org/p/chromium/issues/detail?id=1336156">Chrome bug/limitation</a></p>
				</div>
			</div>
	                <div class="row" id="discover-crashes">
	                        <div class="col-1">Step 5.</div>
	                        <div class="col">Discover crashes</div>
	                        <div class="col-2" id="status"></div>
	                </div>
	                <div class="row" id="discover-saves">
	                        <div class="col-1">Step 6.</div>
	                        <div class="col">Discover save games</div>
	                        <div class="col-2" id="status"></div>
	                </div>
			<div class="row" id="generate-mod-checksums">
				<div class="col-1">Step 7.</div>
				<div class="col">Generate mod file SHA1 checksums</div>
				<div class="col-2" id="status">
					<div class="progress">
						  <div class="progress-bar bg-success" role="progressbar" aria-label="Mod file SHA1 Checksum progress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>
			<div class="row" id="mod-search">
				<div class="col-1">Step 8.</div>
				<div class="col">Checking mod file checksums against database to find mod versions</div>
				<div class="col-2" id="status">
					<div class="progress">
						<div class="progress-bar bg-success" role="progressbar" aria-label="Mod file SHA1 Checksum progress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="mods-tab-pane" role="tabpanel">
			<div class="row mb-5 collapse" id="discover-mods-table">
				<div class="col-1"></div>
				<table class="table">
					<thead><tr><th>Order</th><th>Mod Name</th><th>Supported Version</th><th>Detected</th><th>Latest</th><th>Steam ID</th><th>Valid</th></tr>
					<tbody></tbody>
				</table>
				<div class="col-2"></div>
			</div>
		</div>
		<div class="tab-pane" id="crashes-tab-pane" role="tabpanel">
			<div class="row mb-5 collapse" id="discover-crashes-table">
				<div class="col-1"></div>
					<table class="table">
						<thead><tr><th>Folder</th><th>Last Modified Date</th><th>Valid</th></tr></thead>
				 		<tbody></tbody>
	        	                </table>
				<div class="col-2"></div>
			</div>
		</div>
		<div class="tab-pane" id="saves-tab-pane" role="tabpanel">
	                <div class="row mb-5 collapse" id="discover-saves-table">
			<table class="table">
				<thead><tr><th>File</th><th>Last Modified</th><th>Name</th><th>In game Date</th><th>Fleets</th><th>Planets</th><th>Ironman</th><th>Valid</th></tr></thead>
                                <tbody></tbody>
                        </table>
	                </div>
		</div>
		<div class="tab-pane" id="save-as-tab-pane" role="tabpanel">
			<div class="row">
				This button no workie workie yet.
			</div>
		</div>

	</div>
	<script>
var current_version = '3.4.5';

let stellaris_dir;
let stellaris_crash_dir;
let stellaris_mod_dir;
let stellaris_save_dir;
let steam_workshop_dir;

let continue_game;
let dlc_load;
let game_data;
let mods_registry;

document.getElementById('file-input-button').addEventListener('click', async () => {
	if(!('showDirectoryPicker' in window)){
		alert("Your browser doesn't support the native filesystem API :( Use chrome or edge");
	}
	try {

		stellaris_dir = await window.showDirectoryPicker({id: 'stellaris', startIn: 'desktop'});
	} catch(e) {
		console.log(e);
	}

	try_collect_data(stellaris_dir);
});

document.getElementById('link-steam-workshop-retry').addEventListener('click', async() => {
	try_collect_data(stellaris_dir);
});

async function try_collect_data(stellaris_dir){
	try {
		for await (const entry of stellaris_dir.values()) {
			//console.log(`stellaris dir: ${entry.name} - ${entry.kind}`);
			if(entry.kind == 'file'){
				var handle = await stellaris_dir.getFileHandle(entry.name);
				var file = await handle.getFile();
			}
	
			if(entry.name == 'continue_game.json'){
				continue_game = JSON.parse(await file.text());
			}else if(entry.name == 'dlc_load.json'){
				dlc_load = JSON.parse(await file.text());
			}else if(entry.name == 'game_data.json'){
				game_data = JSON.parse(await file.text());
			}else if(entry.name == 'mods_registry.json'){
				mods_registry = JSON.parse(await file.text());
			}else if(entry.name == 'crashes'){
				stellaris_crash_dir = await stellaris_dir.getDirectoryHandle(entry.name); 
			}else if(entry.name == 'mod'){
				stellaris_mod_dir = await stellaris_dir.getDirectoryHandle(entry.name); 
			}else if(entry.name == 'save games'){
				stellaris_save_dir = await stellaris_dir.getDirectoryHandle(entry.name); 
			}else if(entry.name == 'steam_workshop'){
				steam_workshop_dir = await stellaris_dir.getDirectoryHandle(entry.name);
			}
		}
	
		if(game_data == undefined){
			$('#select-folder-error').removeClass('collapse');
			$('#select-folder > #status').html('<i class="bi bi-x-lg" style="color: red;"></i>');
			return;
		}else{
			$('#select-folder-error').addClass('collapse');
			$('#select-folder > #status').html('<i class="bi bi-check-lg" style="color: green;"></i>');
		}
	
		if(steam_workshop_dir == undefined){
			$('#link-steam-workshop-error').removeClass('collapse');
			$('#link-steam-workshop > #status').html('<i class="bi bi-x-lg" style="color: red;"></i>');
			return;
		}else{
			$('#link-steam-workshop-error').addClass('collapse');
			$('#link-steam-workshop > #status').html('<i class="bi bi-check-lg" style="color: green;"></i>');
		}
	
		if(dlc_load != undefined && mods_registry != undefined){
			$('#discover-meta > #status').html('<i class="bi bi-check-lg" style="color:green;"></i>');
		}else{
			$('#discover-meta > #status').html('<i class="bi bi-x-lg" style="color: red;"></i>');
		}
	
		$('#file-input-button').addClass('disabled').removeClass('btn-primary').addClass('btn-secondary');
		discover_mods();
		discover_crashes();
		discover_save_games();
			
	} catch(e) {
		console.log(e);
	}
}

let mod_list;
let mod_publishedfileid_to_id;
let mod_count;
let success_count;

async function discover_mods(){
	mod_count = 0;
	success_count = 0;

	mod_list = {};
	mod_publishedfileid_to_id = {};
	for (const entry of dlc_load['enabled_mods'].values()){
		//console.log(`enabled mod: ${entry}`);
		if(entry.substring(0,4) == 'mod/'){
			try{
				var mod_filename = entry.substring(4);
				//mod_filename = mod_filename.replaceAll('~', '\\~');
				var fileHandle = await stellaris_mod_dir.getFileHandle(mod_filename);

				var file = await fileHandle.getFile();
				mod_list[mod_count] = {
					mod_file: mod_filename, 
					last_modified: file.lastModified,
					last_modified_date: file.lastModifiedDate,
					size: file.size,
					text: await file.text(),
				};

				mod_list[mod_count]['arr'] = parse_mod_file(mod_list[mod_count]['text']);

				mod = mod_list[mod_count]['arr'];

				mod['id'] = mod_count;
				mod_publishedfileid_to_id[mod['remote_file_id']] = mod_count;

				success_count++;
				pattern = mod['supported_version'];
				pattern.replaceAll('*', '[^\.]+');
				const pattern_re = new RegExp(pattern);


				let this_row = $(
					`<tr id='${mod_count}'>
					<td>${mod_count}</td>
					<td>${mod['name']}</td>
					<td id='ver'>${mod['supported_version']}</td>
					<td id='rev'></td>
					<td id='max_rev'></td>
					<td id='remote'><a href="https://steamcommunity.com/sharedfiles/filedetails/?id=${mod['remote_file_id']}" target="about:blank"><i class="bi bi-steam"></i></a></td>
					<td><i class="bi bi-check-square-fill" style="color:green;"></i></td>
					</tr>`
				);

				let ver = this_row.children('#ver');

				if(!(pattern_re.test(current_version))){
					ver.addClass('table-warning');
				}

				$('#discover-mods-table > table > tbody').append(this_row);
				$('#discover-mods-table').removeClass('collapse');

				let current_mod_dir;

				try {
					current_mod_dir = await stellaris_mod_dir.getDirectoryHandle(mod['path'].split('/').reverse()[0]);
				} catch { };

				if(current_mod_dir == undefined){
					current_mod_dir = await steam_workshop_dir.getDirectoryHandle(mod['remote_file_id']);
				}
				mod['checksum_list'] = recursiveChecksumDirFiles(current_mod_dir, current_mod_dir.name);



				api_file_search(mod);

			}catch(e){
				console.log(`dbg: parse ${mod_filename}`);
				$(' #discover-mods-table > table > tbody').append($(`<tr id='${mod_count}'>
					<td>${mod_count}</td><td>${mod_filename}</td><td colspan=4></td><td><i class="bi bi-x-square-fill" style="color:red;"></i></td></tr>`));
					
				console.log(e);
				$('#discover-mods-bug-warning').removeClass('collapse');
			}

		}
		mod_count++;

	}
	if(success_count == mod_count){
		$('#discover-mods > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i>(${mod_count})`);
	}else if(success_count == 0){
		$('#discover-mods > #status').html('<i class="bi bi-x-lg" style="color:red;"></i>');
	}else{
		$('#discover-mods > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i>${success_count} <i class="bi bi-x-lg" style="color:red;"></i> ${(mod_count-success_count)}`);
	}
		//$('#checklist > #discover-mods > div > table').DataTable();
}


let mod_search_success = 0;
let mod_search_fail = 0;
async function api_file_search(mod){
	//TODO: Remove anything but SHA1 and publishedfileid from submission and API
	await mod['checksum_list'].then(data => 
		$.ajax({
			type: 	"POST",
			url: 	'/api/v1/file_search/' + mod['remote_file_id'], 
			data:	JSON.stringify(data),
			success: function(result){
				row = $('div#discover-mods-table > table > tbody > tr#' + mod_publishedfileid_to_id[result['publishedfileid']]);

				if(result['matches'] == false){
					row.find('#rev').text('No Match').addClass('table-secondary');
					mod_search_success++; //Not really a success but a "successful" result
					$('#mod-search > #status > .progress > .progress-bar').width((mod_search_success/success_count*100) + '%' ).text(mod_search_success + ' / ' + success_count);
					return
				}

				rev = result['matches'][0]['revision_change_number'];
				max_rev = result['matches'][0]['max_rev'];
				row.find('#rev').text(rev);
				row.find('#max_rev').text(max_rev);

				if(rev < max_rev){
					row.find('#rev').addClass('table-danger');
				}else if(rev == max_rev){
					row.find('#rev').addClass('table-success');
				}

				mod_search_success++;
		                $('#mod-search > #status > .progress > .progress-bar').width((mod_search_success/success_count*100) + '%' ).text(mod_search_success + ' / ' + success_count);
			},
			dataType: "json"
		})
	);
}


function parse_mod_file(mod_file_text){
	result = {}
	file_lines = mod_file_text.split('\n');

	for (const line of file_lines){
		first_equals = line.indexOf('=');
		key = line.substring(0, first_equals);
		value = line.substring(first_equals+1);
		value = value.replace(/^"|"$/gm, '').trim();
		result[key] = value;
	}

	return result;
}


let crashes = {}

async function discover_crashes(){
	if(stellaris_crash_dir == undefined){
		$('#discover-crashes > #status').html('<i class="bi bi-x-lg" style="color:red;"></i>');
		return;
	}

	let crash_count = 0;
	let crash_success = 0;
	for await(const entry of stellaris_crash_dir){
		if(entry[1].kind == 'directory'){
			try{
				crash_handle = await stellaris_crash_dir.getDirectoryHandle(entry[1].name);
				exception_handle = await crash_handle.getFileHandle('exception.txt');
				meta_handle = await crash_handle.getFileHandle('meta.yml');
				logs_dir_handle = await crash_handle.getDirectoryHandle('logs');
				error_log_handle = await logs_dir_handle.getFileHandle('error.log');
	
				exception_file = await exception_handle.getFile();
				meta_file = await meta_handle.getFile();
				error_log_file = await error_log_handle.getFile();
	
				exception_text = await exception_file.text();
				meta_text = await meta_file.text();
				error_log_text = await error_log_file.text();
				$('#discover-crashes-table > table > tbody').append(
  	                                $(`<tr><td>${entry[1].name}</td><td>${exception_file.lastModifiedDate}</td><td class="text-end"><i class="bi bi-check-square-fill" style="color:green;"></i></td></tr>`)
				);
				crash_success++;
			} catch(e){
				console.log(`Exception while processing crash (${entry[1].name}): ${e}`);
				$('#discover-crashes-table >  table > tbody').append(
					$(`<tr><td>${entry[1].name}</td><td></td><td class="text-end"><i class="bi bi-x-square-fill" style="color:red;"></i></td></tr>`)
				);
			}

			crash_count++;
		}
	}

	if(crash_count == 0){
		$('#discover-crashes > #status').html('<i class="bi bi-x-lg" style="color:red;"></i> (No crashes found)');
	}else if (crash_count == crash_success){
		$('#discover-crashes > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i> (${crash_count})`);
	}else{
		$('#discover-crashes > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i> (${crash_success}) <i class="bi bi-x-lg" style="color:red;"></i> (${crash_count-crash_success})`);
	}

	$('#discover-crashes-table').removeClass('collapse');
	$('#discover-crashes-table >  table').DataTable({
		dom: 'rtip',
		columns: [
		{data: 'name'},
		{data: 'last_modified_date', render: DataTable.render.datetime()},
		{data: 'validation'},
		],
		order: [[1, 'desc']],
		searching: false,
	});
}

let save_games = {};

async function discover_save_games(){
	if(stellaris_save_dir == undefined){
		$('#discover-saves > #status').html('<i class="bi bi-x-lg" style="color:red;"></i>');
		return;
	}

	let save_count = 0;
	let save_success = 0;

	result = await discover_save_game_dir(stellaris_save_dir, save_count, save_success);
	save_count = result[0];
	save_success = result[1];

	if(save_count == 0){
		$('#discover-saves > #status').html('<i class="bi bi-x-lg" style="color:red;"></i>');
	}else if(save_success == save_count){
		$('#discover-saves > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i> ${save_success}`);
	}else{
		$('#discover-saves > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i> ${save_success} <i class="bi bi-x-lg" style="color:red;"></i> ${save_count-save_success}`);
	}

	$('#discover-saves-table').removeClass('collapse');
	$('#discover-saves-table > table').DataTable({
		dom: 'frtip',
		columns: [
			{data: 'file_name'},
			{data: 'last_modified_date', render: DataTable.render.datetime()},
		{data: 'in_game_name'},
		{data: 'in_game_date'},
		{data: 'in_game_fleets'},
		{data: 'in_game_planets'},
		{data: 'in_game_ironman'},
		{data: 'validation'},
		],
		rowGroup: { dataSrc: 'in_game_name' },
		order: [[1, 'desc']],

		});

}

async function discover_save_game_dir(directory_handle, save_count, save_success){
	for await(const entry of directory_handle){
		if(entry[1].kind == 'directory'){
			console.log(`entering dir ${entry[1].name}`);
			result = await discover_save_game_dir(entry[1], save_count, save_success);
			save_count = result[0];
			save_success = result[1];
		}else{
			file_handle = entry[1];
			file_name = file_handle.name;

			if(file_name.substr(-3) == 'sav'){
				try {
					var file = await file_handle.getFile();
					var meta = await readZipMeta(file);
					if(meta == ""){
						throw Exception('Invalid save file');
					}

					save_success++;
					save_meta = parse_mod_file(meta);
					if(save_meta['ironman'] == undefined){
						save_meta['ironman'] = 'no';
					}
					$('#discover-saves-table > table > tbody').append($(`<tr>
						<td>${file_name}</td>
						<td>${file.lastModifiedDate}</td>
						<td>${save_meta['name']}</td>
						<td>${save_meta['date']}</td>
						<td>${save_meta['meta_fleets']}</td>
						<td>${save_meta['meta_planets']}</td>
						<td>${save_meta['ironman']}</td>
						<td class="text-end"><i class="bi bi-check-square-fill" style="color:green;"></i></td>
						</tr>`));
				} catch(e) {
					$('#discover-saves-table > table > tbody').append($(`<tr>
						<td>${entry[1].name} - ${entry[1].kind}</td>
						<td colspan=6></td>
						<td class="text-end"><i class="bi bi-x-square-fill" style="color:red;"></i></td>
						</tr>`));
					console.log(e)
				}
				save_count++;
			}
		}
	}

	return [save_count, save_success];
}


let mod_checksum_count = 0;

//From https://stackoverflow.com/questions/64283711/how-to-recursively-read-local-files-and-directories-in-web-browser-using-file-sy
async function recursiveChecksumDirFiles(dirHandle, path) {
	const files = [];
	for await (let [name, handle] of dirHandle) {
		const {kind} = handle;
		if (handle.kind === 'directory') {
			//files.push({path, name, handle, kind});
			files.push(...await recursiveChecksumDirFiles(handle, path + '/' + name));
		} else {
			const fullname = path + '/' + name;
			let sha1, lastModified, size;
			try {
				var file = await handle.getFile()
				sha1 = await getSHA1FromUint8Array(await file.arrayBuffer());
				lastModified = file.lastModified;
				size = file.size;
			} catch(e) {
				console.log(fullname + ' - Exception - ' + e);
			}

			if(sha1 == undefined){
				sha1 = null;
			}
			if(lastModified == undefined){
				lastModified = null;
			}
			if(size == undefined){
				size = null;
			}

		        files.push({fullname, lastModified, size, sha1});
		}
	}
	if(!path.includes('/')){
		mod_checksum_count++;
		$('#generate-mod-checksums > #status > .progress > .progress-bar').width((mod_checksum_count/success_count*100) + '%' ).text(mod_checksum_count + ' / ' + success_count);

	}

	return files;
}


// From https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/digest
async function getSHA1FromText(message) {
	const msgUint8 = new TextEncoder().encode(message);
	const hashBuffer = await crypto.subtle.digest('SHA-1', msgUint8); 
	const hashArray = Array.from(new Uint8Array(hashBuffer));
	const hashHex = hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');


	return hashHex;
}

async function getSHA1FromUint8Array(message) {
        const hashBuffer = await crypto.subtle.digest('SHA-1', message); 
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');

        return hashHex;
}


async function generate_checksums(directory_handle){

}

</script>
        <script type="text/javascript" src="lib/zip.js/dist/zip.min.js"></script>
        <script type="text/javascript" src="lib/zip.js/dist/z-worker.js"></script>
	<script type="text/javascript" src="zip-local.js"></script>
	<!--<script src="//cdn.jsdelivr.net/npm/@cronvel/minimatch@3.0.2/minimatch.min.js" integrity="sha384-mmkNRL4JbVe0XT4bBh3RNLYbCu9uXARw5CozhYqsKY1RNWSsTsG//gaMI8mdaTZw" crossorigin="anonymous" referrerpolicy="no-referrer"></script>-->

<?php
	require_once('../stellar-web/footer.php');
?>
	<script>
	$(document).ready( function() {
		        if(!('showDirectoryPicker' in window)){
				                $('#native-file-api-err > .col > .alert').removeClass('collapse');
						        }
	});
	</script>
