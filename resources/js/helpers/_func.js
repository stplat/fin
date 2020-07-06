/* Определение времени выполнения скрипта */
/*let time = performance.now();
console.log('Выполняем')
time = performance.now() - time;
console.log('Время выполнения = ', time);*/

/* Транслитерация */
export function translit(str) {
  const rus = ['а', 'в', 'е', 'к', 'м', 'н', 'о', 'р', 'с', 'т', 'у', 'х'];
  const lat = ['a', 'b', 'e', 'k', 'm', 'h', 'o', 'p', 'c', 't', 'y', 'x'];

  return str.split('').map(char => {
    if (rus.indexOf(char) !== -1) {
      return lat[rus.indexOf(char)];
    }
    return char;
  }).join('');
}

/* Формат дня недели */
export function formatDate(date) {
  const year = date.getFullYear();
  const month = date.getMonth() < 9 ? '0' + Number(date.getMonth() + 1) : Number(date.getDate() + 1);
  const day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();

  return `${ year }-${ month }-${ day }`;
}

/* Формат времени */
export function formatTime(date = new Date()) {
  const hours = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
  const minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
  const seconds = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();

  return `${ hours }:${ minutes }`;
}

/* Получить день недели */
export function getWeekDay(date) {
  date = date || new Date();
  var days = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
  var day = date.getDay();

  return days[day];
}

/* Первый день последних месяца/квартала/года */
export function currentDate(date = new Date()) {
  const year = date.getFullYear();
  const month = date.getMonth() + 1 < 10 ? '0' + ( date.getMonth() + 1 ) : date.getMonth() + 1;
  const day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();

  return `${ year }-${ month }-${ day }`;
}

export function firstDayCurrentMonth(date = new Date()) {
  const year = date.getFullYear();
  const month = date.getMonth() + 1 < 10 ? '0' + ( date.getMonth() + 1 ) : date.getMonth() + 1;
  const day = '01';

  return `${ year }-${ month }-${ day }`;
}

export function firstDayCurrentQuarter(date = new Date()) {
  const year = date.getFullYear();
  let month = date.getMonth() + 1;
  if (month <= 3) month = 1;
  if (month <= 6) month = 2;
  if (month <= 9) month = 3;
  if (month <= 12) month = 4;
  month = month < 10 ? '0' + month : month;
  const day = '01';

  return `${ year }-${ month }-${ day }`;
}

export function firstDayCurrentYear(date = new Date()) {
  const year = date.getFullYear();
  const month = '01';
  const day = '01';

  return `${ year }-${ month }-${ day }`;
}


/* Дистанция между точкой и прямой на карте по широте / долготе */
export function distanceToLine(origin, destination) {
  let { point1, point2 } = origin;

  const x0 = toRadian(destination[0]),
    y0 = toRadian(destination[1]),
    x1 = toRadian(point1[0]),
    y1 = toRadian(point1[1]),
    x2 = toRadian(point2[0]),
    y2 = toRadian(point2[1]);

  // {point1: [1, 2], point2: [2, 4]}

  const numerator = Math.abs(( y2 - y1 ) * x0 - ( x2 - x1 ) * y0 + x2 * y1 - y2 * x1),
    denominator = Math.sqrt(Math.pow(( y2 - y1 ), 2) + Math.pow(( x2 - x1 ), 2));

  const EARTH_RADIUS = 6371;
  return numerator / denominator * EARTH_RADIUS * 1000;
}

/* Дистанция между точками на карте по широте / долготе */
export function getDistance(origin, destination) {
  // return distance in meters
  const lon1 = toRadian(origin[1]),
    lat1 = toRadian(origin[0]),
    lon2 = toRadian(destination[1]),
    lat2 = toRadian(destination[0]);

  const deltaLat = lat2 - lat1;
  const deltaLon = lon2 - lon1;

  const a = Math.pow(Math.sin(deltaLat / 2), 2) + Math.cos(lat1) * Math.cos(lat2) * Math.pow(Math.sin(deltaLon / 2), 2);
  const c = 2 * Math.asin(Math.sqrt(a));
  const EARTH_RADIUS = 6371;
  return c * EARTH_RADIUS * 1000;
}

function toRadian(degree) {
  return degree * Math.PI / 180;
}
