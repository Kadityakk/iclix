import Alpine from 'alpinejs';
import { contentRow, heroSlider, liveSearch, uploadForm, videoPlayer } from './components';
import { registerParallax, registerReveal } from './parallax';
import { registerStores } from './stores';

window.Alpine = Alpine;

registerParallax(Alpine);
registerReveal(Alpine);
registerStores(Alpine);

Alpine.data('heroSlider', heroSlider);
Alpine.data('contentRow', contentRow);
Alpine.data('liveSearch', liveSearch);
Alpine.data('videoPlayer', videoPlayer);
Alpine.data('uploadForm', uploadForm);

Alpine.start();
