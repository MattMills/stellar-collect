<?php
	$this_page = 'saves';
	require_once('../stellar-web/header.php');
?>
	<div id="container" class="container-fluid" style="width:80%">
		<div class="row">
			<h2>(EARLY DEVELOPMENT)</h2>
		</div>
		<div class="row">&nbsp;</div>
		<div class="row">&nbsp;</div>
		<div class="row" id="checklist">
			<div class="col-9 align-self-center" id="select-folder">
				<div class="row">
				<div class="col-2">Step 1.</div>
				<div class="col-6"><button id="file-input-button" class="btn btn-primary">Browse to My Documents \ Paradox Interactive \ Stellaris folder</button></div>
				<div class="col-1" id="status">?</div>
				</div>
			</div>
			<div class="col-9 align-self-center" id="discover-meta">
                                <div class="row">
	                                <div class="col-2">Step 2.</div>
	                                <div class="col-6">Discover game metadata</div>
	                                <div class="col-1" id="status">?</div>
                                </div>
                        </div>
			<div class="col-9 align-self-center" id="discover-mods">
				<div class="row">
					<div class="col-2">Step 3.</div>
					<div class="col-6">Discover enabled mods</div>
					<div class="col-3" id="status">?</div>
				</div>
				<div class="row">
				<table class="table">
				<tbody></tbody>
				</table>
				</div>
			</div>
                        <div class="col-9 align-self-center" id="discover-crashes">
                                <div class="row">
                                        <div class="col-2">Step 4.</div>
                                        <div class="col-6">Discover crashes</div>
                                        <div class="col-3" id="status">?</div>
                                </div>
                                <div class="row">
                                <table class="table">
                                <tbody></tbody>
                                </table>
                                </div>
                        </div>
                        <div class="col-9 align-self-center" id="discover-saves">
                                <div class="row">
                                        <div class="col-2">Step 5.</div>
                                        <div class="col-6">Discover save games</div>
                                        <div class="col-3" id="status">?</div>
                                </div>
                                <div class="row">
                                <table class="table">
                                <tbody></tbody>
                                </table>
                                </div>
                        </div>

		</div>

	</div>
	<script>
	var current_version = '3.4.*';
	//TODO: Make GLOB work

	let stellaris_dir;
	let stellaris_crash_dir;
	let stellaris_mod_dir;
	let stellaris_save_dir;

	let continue_game;
	let dlc_load;
	let game_data;
	let mods_registry;

	document.getElementById('file-input-button').addEventListener('click', async () => {
		try {
			stellaris_dir = await window.showDirectoryPicker({id: 'stellaris', startIn: 'desktop'});
			//TODO: Check that this is the right directory
			$('#checklist > #select-folder > div > #status').html('<h1><i class="bi bi-check-lg" style="color: green;"></i></h1>');

			for await (const entry of stellaris_dir.values()) {
				//console.log(`stellaris dir: ${entry.name} - ${entry.kind}`);
				if(entry.kind == 'file'){
					handle = await stellaris_dir.getFileHandle(entry.name);
					file = await handle.getFile();
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
				}

			}

			if(dlc_load != undefined && mods_registry != undefined){
				$('#checklist > #discover-meta > div > #status').html('<i class="bi bi-check-lg" style="color:green;"></i>');
			}

			discover_mods();
			discover_crashes();
			discover_save_games();

		} catch(e) {
			console.log(e);
		}
	});

	let mod_list;

	async function discover_mods(){
		let mod_count = 0;
		let success_count = 0;

		mod_list = {};
		for (const entry of dlc_load['enabled_mods'].values()){
			//console.log(`enabled mod: ${entry}`);
			if(entry.substring(0,4) == 'mod/'){
				try{
					mod_filename = entry.substring(4);
					//mod_filename = mod_filename.replaceAll('~', '\\~');
					fileHandle = await stellaris_mod_dir.getFileHandle(mod_filename);

					file = await fileHandle.getFile();
					mod_list[mod_count] = {
						mod_file: mod_filename, 
						last_modified: file.lastModified,
						last_modified_date: file.lastModifiedDate,
						size: file.size,
						text: await file.text(),
					};

					mod_list[mod_count]['arr'] = parse_mod_file(mod_list[mod_count]['text']);

					mod = mod_list[mod_count]['arr'];

					success_count++;
					$('#checklist > #discover-mods > div > table > tbody').append($(`<tr id='${mod_count}'><td>${mod['name']}</td><td id='ver'>${mod['supported_version']}</td><td id='remote'>${mod['remote_file_id']}</td><td><i class="bi bi-check-square-fill" style="color:green;"></i></td></tr>`));
					let this_row = $(`#checklist > #discover-mods > div > table > tbody > #${mod_count}`);
					let ver = this_row.children('#ver');
					if(ver.html() != current_version){
						ver.addClass('table-warning');
					}

				}catch(e){
					console.log(`dbg: parse ${mod_filename}`);
					$('#checklist > #discover-mods > div > table > tbody').append($(`<tr><td>${mod_filename}</td><td></td><td></td><td><i class="bi bi-x-square-fill" style="color:red;"></i></td></tr>`));
					
					console.log(e);
				}
			}
			mod_count++;

		}
		if(success_count == mod_count){
			$('#checklist > #discover-mods > div > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i>(${mod_count})`);
		}else if(success_count == 0){
			$('#checklist > #discover-mods > div > #status').html('<i class="bi bi-x-lg" style="color:red;"></i>');
		}else{
			$('#checklist > #discover-mods > div > #status').html(`<i class="bi bi-check-lg" style="color:green;"></i>${success_count} <i class="bi bi-x-lg" style="color:red;"></i> ${(mod_count-success_count)}`);
		}
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
			$('#checklist > #discover-crashes > div > #status').html('<i class="bi bi-x-lg" style="color:red;"></i>');
			return;
		}

		let crash_count = 0;
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
					$('#checklist > #discover-crashes > div > table > tbody').append(
  	                                      $(`<tr><td>${entry[1].name}</td><td>${exception_file.lastModifiedDate}</td><td><i class="bi bi-check-square-fill" style="color:green;"></i></td></tr>`)
	                                );
				} catch(e){
					console.log(`Exception while processing crash (${entry[1].name}): ${e}`);
					$('#checklist > #discover-crashes > div > table > tbody').append(
						$(`<tr><td>${entry[1].name}</td><td></td><td><i class="bi bi-x-square-fill" style="color:red;"></i></td></tr>`)
					);
				}

				crash_count++;
			}
		}
	}

	let save_games = {};

	async function discover_save_games(){
		if(stellaris_save_dir == undefined){
			$('#checklist > #discover-saves > div > #status').html('<i class="bi bi-x-lg" style="color:red;"></i>');
			return;
		}
		for await(const entry of stellaris_save_dir){

                        $('#checklist > #discover-saves > div > table > tbody').append($(`<tr><td>${entry[1].name} - ${entry[1].kind}</td></tr>`));
                }

	}
</script>

<?php
	require_once('../stellar-web/footer.php');
?>
