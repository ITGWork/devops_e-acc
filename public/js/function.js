function DateThai(date, plus_year){
  var month = Array();
      month['01'] = 'ม.ค.';
      month['02'] = 'ก.พ.';
      month['03'] = 'มี.ค.';
      month['04'] = 'เม.ย.';
      month['05'] = 'พ.ค.';
      month['06'] = 'มิ.ย.';
      month['07'] = 'ก.ค.';
      month['08'] = 'ส.ค.';
      month['09'] = 'ก.ย.';
      month['10'] = 'ต.ค.';
      month['11'] = 'พ.ย.';
      month['12'] = 'ธ.ค.';

  if (date) {
    var dates = date.split('-');
    var year = (plus_year!==false)?parseInt(dates[0])+543:dates[0];
    return dates[2]+' '+month[dates[1]]+' '+year;
  } else {
    return 'n/a';
  }

}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
