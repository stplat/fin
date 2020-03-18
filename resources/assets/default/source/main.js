import './style.scss';

/* Скрипты по умолчанию */
import './components/_defaults/defaults';

/* React */
import React from 'react';
import { render } from 'react-dom';

import App from './components/header/Header';

render(<App/>, document.querySelector('body'));
