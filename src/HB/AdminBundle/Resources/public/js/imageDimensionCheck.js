function checkImageDimension(file, maxWidth, maxHeight) {
    return new Promise(
        function (resolve, reject) {
            const blob = file.originalFile;
            const url = URL.createObjectURL(blob);
            const img = new Image();
            img.src = url;

            img.onload = function () {

                URL.revokeObjectURL(url);

                if (this.naturalWidth > maxWidth) {
                    reject('Максимальная ширина картинки ' + maxWidth + ' пикселей');
                }
                if (this.naturalHeight > maxHeight) {
                    reject('Максимальная высота картинки ' + maxHeight + ' пикселей');
                }
                resolve();
            }
        }
    );
}