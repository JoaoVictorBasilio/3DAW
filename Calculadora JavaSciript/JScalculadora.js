let a = "";
let b = "";
let operador = "";
let novoNumero = false;

function atualizarTela(){

    document.getElementById("result").innerHTML = b || "0";

    if(a != "" && operador != ""){
        document.getElementById("expr").innerHTML = a + " " + operador;
    }
    else{
        document.getElementById("expr").innerHTML = "";
    }
}

function numero(valor){

    if(novoNumero){
        b = valor;
        novoNumero = false;
    }
    else{

        if(b == "0"){
            b = valor;
        }
        else{
            b += valor;
        }

    }

    atualizarTela();
}

function virgula(){

    if(!b.includes(",")){

        if(b == ""){
            b = "0,";
        }
        else{
            b += ",";
        }

    }

    atualizarTela();
}

function operacao(op){

    if(b == "") return;

    if(a != "" && operador != "" && !novoNumero){
        calcular();
    }

    a = b;
    operador = op;
    novoNumero = true;

    atualizarTela();
}

function calcular(){

    if(a == "" || operador == "" || b == "") return;

    let n1 = parseFloat(a.replace(",", "."));
    let n2 = parseFloat(b.replace(",", "."));

    let resultado = 0;

    if(operador == "+"){
        resultado = n1 + n2;
    }

    else if(operador == "-"){
        resultado = n1 - n2;
    }

    else if(operador == "*"){
        resultado = n1 * n2;
    }

    else if(operador == "/"){

        if(n2 == 0){
            b = "Erro";
            atualizarTela();
            return;
        }

        resultado = n1 / n2;
    }

    else if(operador == "%"){
        resultado = n1 * (n2 / 100);
    }

    b = resultado.toString().replace(".", ",");

    a = "";
    operador = "";
    novoNumero = true;

    atualizarTela();
}

function igual(){
    calcular();
}

function limpar(){

    a = "";
    b = "";
    operador = "";
    novoNumero = false;

    atualizarTela();
}

function apagar(){

    if(b.length > 1){
        b = b.slice(0, -1);
    }
    else{
        b = "0";
    }

    atualizarTela();
}

document.addEventListener("keydown", function(event){

    if(event.key >= "0" && event.key <= "9"){
        numero(event.key);
    }

    else if(event.key == "+"){
        operacao("+");
    }

    else if(event.key == "-"){
        operacao("-");
    }

    else if(event.key == "*"){
        operacao("*");
    }

    else if(event.key == "/"){
        operacao("/");
    }

    else if(event.key == "%"){
        operacao("%");
    }

    else if(event.key == "Enter"){
        igual();
    }

    else if(event.key == "Backspace"){
        apagar();
    }

    else if(event.key == "Escape"){
        limpar();
    }

    else if(event.key == "," || event.key == "."){
        virgula();
    }

});

atualizarTela();