function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

window.onload = async function () {
    let elem = document.getElementById('header');
    for (let i = 0; i < 50; i += 0.2) {
        elem.style.setProperty('--startAnimX', i + 'vw');
        await sleep(7);
    }
    for (let i = 50; i > 30; i -= 0.5) {
        elem.style.setProperty('--startAnimY', i + 'vh');
        await sleep(2);
    }
    for (let i = 30; i < 50; i += 0.5) {
        elem.style.setProperty('--startAnimY', i + 'vh');
        await sleep(4);
    }

    let i =3;
    let j = 50;
    let k = 50;
    while (i < 180) {
        elem.style.setProperty('--circlePos', i + 'vmax');
        elem.style.setProperty('--startAnimX', j + 'vh');
        elem.style.setProperty('--startAnimY', k + 'vh');
        i+= 1;
        j-=0.5;
        k+=0.5;
        await sleep(4);
    }

    document.getElementById('headerContent').style.display="flex";
    document.getElementById('navbar').style.display="block";
}

