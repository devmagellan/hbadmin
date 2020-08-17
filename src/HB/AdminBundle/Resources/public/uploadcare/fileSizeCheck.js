function maxFileSize(size) {
    return function (fileInfo) {
        if (fileInfo.size !== null && fileInfo.size > size) {
            //todo: open modal with error message
            openModal('Ошибка при загрузке файла', 'Максимальный объем файла ' + (size / 1024)+' Mb');
            closeHoldon();
            throw new Error("fileMaximumSize");
        }
    };
}