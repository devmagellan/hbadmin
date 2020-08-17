function fileExtension(allowed) {
    return function(fileInfo) {
        if (fileInfo.name) {
            var ext = fileInfo.name.split('.')[1];

            console.log(ext);
            console.log(fileInfo);
            console.log(allowed);
            console.log(allowed.indexOf(ext));

            if (allowed.indexOf(ext) < 0) {
                alert('Недопустимое расширение файла. Разрешено: '+allowed.join(',') );
                throw new Error('fileExtensionError');
            }
        }
    };
}