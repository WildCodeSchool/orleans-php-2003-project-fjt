function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

let mainColor="rgb(255, 0, 83)";
let secondColor="#032497";

window.onload = async function () {
    let elem = document.getElementById('circle');
    elem.style.backgroundImage = "radial-gradient(circle 3vmax at 50vw 50vh , "+mainColor+" 10%, "+mainColor+" 57%, "+secondColor+" 60%)";
    for (let i = 0; i < 50; i += 0.2) {
        elem.style.backgroundImage = "radial-gradient(circle 3vmax at " + i + "vw 50vh , "+mainColor+" 10%, "+mainColor+" 57%, "+secondColor+" 60%)";
        await sleep(7);
    }
    for (let i = 50; i > 30; i -= 0.5) {
        elem.style.backgroundImage = "radial-gradient(circle 3vmax at 50vw " + i + "vh , "+mainColor+" 10%, "+mainColor+" 57%, "+secondColor+" 60%)";
        await sleep(2);
    }
    for (let i = 30; i < 50; i += 0.5) {
        elem.style.backgroundImage = "radial-gradient(circle 3vmax at 50vw " + i + "vh , "+mainColor+" 10%, "+mainColor+" 57%, "+secondColor+" 60%)";
        await sleep(4);
    }
    let i =3;
    let j = 50;
    let k = 50;
    while (i < 300) {
        elem.style.backgroundImage = "radial-gradient(circle " + i + "vmax at "+j+"vw "+k+"vh , "+mainColor+" 10%, "+mainColor+" 60%, "+secondColor+" 60%)";
        i+= 1;
        if(j>-110){
            j-=0.5;
        }
        if(k<180){
            k+=0.5;
        }
        await sleep(4);
    }
    document.getElementById('header').style.display="block";
    document.getElementById('main').style.display="block";
    document.getElementById('footer').style.display="block";
}

