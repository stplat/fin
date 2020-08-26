import Vue from 'vue';
import Unicon from 'vue-unicons';
import {
  uniMapMarker, uniScenery, uniSort, uniAngleDown,
  uniAngleUp, uniMultiply, uniUsersAlt, uniPen, uniTrashAlt, uniCheck, uniTimes, uniPlus, uniPlusSquare,
  uniPlay, uniLineAlt, uniPresentationCheck, uniPresentationPlay, uniFileDownload, uniRepeat, uniAngleRight, uniAngleLeft, uniRedo, uniServerConnection,
  uniFileUpload, uniPause
} from 'vue-unicons/src/icons'

Unicon.add([uniMapMarker, uniScenery, uniSort, uniAngleDown, uniAngleUp,
  uniMultiply, uniUsersAlt, uniPen, uniTrashAlt, uniCheck, uniTimes, uniPlus, uniPlusSquare,
  uniPlay, uniLineAlt, uniPresentationCheck, uniPresentationPlay, uniFileDownload, uniRepeat, uniAngleRight, uniAngleLeft, uniRedo, uniServerConnection,
  uniFileUpload, uniPause
]);
Vue.use(Unicon);

export default Unicon;
