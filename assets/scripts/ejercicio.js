/*
Realiza una función que reciba una lista de números (1 - 50) y me devuelva 
los cuadrados de los numeros que son divisibles entre 3

Pista: .map(), .filter() y operadores aritmeticos
*/

// function procesador(lista = []) {
//     const newLista = lista.filter((kely) => kely % 3 === 0).map((kely) => kely ** 2);
//     return newLista;
// }
// const lista = Array.from({ length: 50 }, (_, i) => i + 1);

/*
Filtrado de Array: Tienes un array con nombres de 
productos y sus precios. Crea una función que reciba 
este array y un presupuesto máximo, y devuelva un nuevo array solo 
con los productos que puedes costear.
*/

const productos = [
    {
        nombre: 'Laptop',
        precio: 1000,
    },
    {
        nombre: 'Mouse',
        precio: 10,
    },
    {
        nombre: 'Teclado',
        precio: 20,
    },
    {
        nombre: 'Monitor',
        precio: 300,
    },
];

function puede_costear(productos = [], presupuesto_maximo = 0) {
    const newproductos = productos.filter((producto) => presupuesto_maximo >= producto.precio);
    return newproductos;
}

console.log(puede_costear(productos, 9));
