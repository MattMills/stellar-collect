meta_contents = null;
gamestate_contents = null;

let selectedFile;
zip.configure({ workerScripts: { inflate: ["z-worker.js"] } });
async function readZipMeta(fileBlob){
	const reader = new zip.ZipReader(new zip.BlobReader(fileBlob));
	const entries = await reader.getEntries();
	var meta_text = "";
	$.each(entries, async function(i, entry){
		const filename = entry.filename;
       		if(filename == "meta"){
              		meta_text = entry.getData(new zip.TextWriter(),{
                               	onprogress: function(index, max){}
       			});
        	}
        });

	await reader.close();

	return meta_text;
}
