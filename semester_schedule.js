function set(){
    for(let i=2014; i<=nextYear; i++){
        let control = parseInt(Yselect.options[i-2014].value, 10);
        if(control === selectedYear){
            console.log("yes");
            Yselect.options[i-2014].selected = true;
            break;
        }else{
            console.log("not");
        }
    }
}

//プルダウン取ってくる
let Yselect = document.getElementById('Yselect');
console.log("プルダウン");
console.log(Yselect.options[0].value);

//値
let selectedYear = parseInt(js_Syear, 10);
let nextYear = parseInt(js_year, 10);
const older = 2014;//最古
console.log(selectedYear);
console.log(nextYear);
console.log(older);

set();