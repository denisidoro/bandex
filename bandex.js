function interpret() {

  var data = JSON.parse(global('HTTPD'))['ru'];

  var day = Object.keys(data)[0]; 
  var meal = Object.keys(data[day])[0];

  return [meal + ', le ' + day, data[day][meal].join('\n')];

}
