function minDimensions(width, height) {
    return function(fileInfo) {
        var imageInfo = fileInfo.originalImageInfo;
        if (imageInfo === null) {
            return;
        }
        var heightExceeded = height && imageInfo.height < height;
        if (width && imageInfo.width < width) {
            if (heightExceeded) {
                throw new Error('minDimensions');
            } else {
                throw new Error('minWidth');
            }
        }
        if (heightExceeded) {
            throw new Error('minHeight');
        }
    };
}