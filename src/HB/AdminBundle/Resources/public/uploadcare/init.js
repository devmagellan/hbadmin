UPLOADCARE_LOCALE = 'ru';

UPLOADCARE_LOCALE_TRANSLATIONS = {
    // messages for widget
    errors: {
        'minDimensions': 'This image exceeds max dimensions.',
        'minWidth': 'This image exceeds max width.',
        'minHeight': 'This image exceeds max height.',
        // 'fileMaximumSize': 'This file has size more then allowed'
    },
    // messages for dialog’s error page
    dialog: {
        tabs: {
            preview: {
                error: {
                    'minDimensions': {
                        title: 'Ошибка размеров картинки',
                        text: 'Минимальные размеры картинки не соответствуют требуемым',
                        back: 'Назад'
                    },
                    'minWidth': {
                        title: 'Ошибка размеров картинки',
                        text: 'Не соответствует минимальная ширина картинки.',
                        back: 'Назад'
                    },
                    'minHeight': {
                        title: 'Ошибка размеров картинки',
                        text: 'Не соответствует минимальная высота картинки.',
                        back: 'Назад'
                    },
                    /*'fileMaximumSize': {
                        title: 'Превышен размер файла',
                        text: 'Максимальный размер файла 2Gb',
                        back: 'Назад'
                    }*/
                }
            }
        }
    }
};