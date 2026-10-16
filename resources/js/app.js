import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
    duration: 4000,
    position: {
        x: 'left',
        y: 'bottom',
    },
    dismissible: true,
    ripple: true,
    types: [
        {
            type: 'warning',
            background: '#e6a23c',
            icon: false,
        },
    ],
});

window.notyf = notyf;