import { postData } from "../../helpers/helpers.js";
let clientName;
const baseUrl = "https://app.samycosmetics.com/api-samy/public/api/";

const getTable = async () => {
  const orderID = new URLSearchParams(window.location.search).get("orderID");
  // const url = '../../controller/router.php';
  const url = `${baseUrl}Orders/Detail/${orderID}`;
  // const data = {
  //   order: orderID,
  //   case: "getOrderDetails",
  // };
  const order = document.getElementById("order_data");
  const table = document.getElementById("data");
  let html = "";

  // const response = await postData(url, data, true);
  const response = await (
    await fetch(url, { headers: { accept: "application/json" } })
  ).json();

  response.forEach((orderCode) => {
    clientName = orderCode.NAME;
    html += `
       <tr>
        <td data-label="Articulo">${orderCode.BAR_CODE}</td>
        <td data-label="Descripción" class="celda-descripcion">
          <div class="Contenedor_descripción">
            <div class="descripcion">${orderCode.description}</div>
          </div>
        </td>
        <td data-label="Cantidad">${parseInt(orderCode.AMOUNT)}</td>
      </tr>
      `;
  });
  const name = formatName(clientName);
  order.innerHTML = `  Pedido ${orderID}<br> ${name}<br>`;
  table.innerHTML = html;
  // test();
};

const formatName = (fullName) => {
  const nameParts = fullName.split(" ");
  const formattedParts = nameParts.map(
    (part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase()
  );
  return formattedParts.join(" ");
};

// const test = () => {
//   const celdasDescripcion = document.querySelectorAll(".celda-descripcion");

//   celdasDescripcion.forEach((celdaDescripcion) => {
//     const contenedorDescripcion = celdaDescripcion.querySelector(".Contenedor_descripción");
//     const descripcionResumen = celdaDescripcion.querySelector(".descripcion_resumen");
//     const descripcionCompleta = celdaDescripcion.querySelector(".descripcion_completa");
//     let mostrandoCompleta = false;

//     celdaDescripcion.addEventListener("click", function () {
//       if (mostrandoCompleta) {

//         descripcionCompleta.style.display = "none";
//         contenedorDescripcion.style.maxHeight = "2.8em"; /* Restaurar la restricción de altura */
//         descripcionResumen.style.display = "block";
//         mostrandoCompleta = false;
//       } else {
//         // Si no se está mostrando la descripción completa, mostrar todo
//         descripcionCompleta.style.display = "block";
//         contenedorDescripcion.style.maxHeight = "none"; /* Eliminar la restricción de altura */
//         descripcionResumen.style.display = "none";
//         mostrandoCompleta = true;
//       }
//     });
//   });
// };

document.addEventListener("DOMContentLoaded", getTable);
