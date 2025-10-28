import {
  postData,
  divLoading,
  numberFormat,
  selectClientDOM,
} from "../../helpers/helpers.js";

const baseUrl = "https://app.samycosmetics.com/api-samy/public/api/";
const urlQuery = new URLSearchParams(window.location.search);
const token = urlQuery.get("token");

const initClientWallet = async (clientToken = false) => {
  divLoading("wallet-client");
  let url = `${baseUrl}Wallet/Client/?token=${token}`;
  if (clientToken) {
    url = `${baseUrl}Wallet/Client/?token=${clientToken}`;
  }
  // const data = {
  //   id: idClient,
  //   case: "infoWallet",
  // };

  // const infoWalletResponse = await postData(url, data);
  const infoWalletResponse = await (
    await fetch(url, { headers: { accept: "application/json" } })
  ).json();

  const fields = {
    "total-debt": numberFormat(infoWalletResponse.deuda),
    "credit-limit": numberFormat(infoWalletResponse.limite),
    "arrears-days": numberFormat(infoWalletResponse.dia_atraso),
    "limit-space": numberFormat(
      infoWalletResponse.cupo_disp - infoWalletResponse.total_pedido
    ),
    "client-name": infoWalletResponse.nombre,
  };

  for (const key in fields) {
    document.querySelector(`#${key}`).textContent = fields[key];
  }
  // console.log(infoWalletResponse);
  divLoading("wallet-client", false);
  document
    .querySelector("#client-name")
    .parentNode.classList.remove("display-none");
};

const showCardMessage = () => {
  const cardMessage = document.createElement("div");
  cardMessage.className = "card-message";
  const message = document.createElement("h1");
  const image = document.createElement("img");
  image.src = "../../assets/img/void-box.svg";
  message.textContent = "No tienes facturas pendientes";
  document.querySelector("body").append(cardMessage);
  // document.querySelector("body main").classList.add("display-none");
  // document.querySelector("body").classList.add("center-items");
  // document.querySelector(".main").style.display = "none";
  cardMessage.appendChild(message);
  cardMessage.appendChild(image);
  // document.querySelector(".section-2").style.display = "none";
};

const resetUIWallet = () => {
  if (document.querySelector(".card-message")) {
    document.querySelector(".card-message").remove();
  }

  const mainElement = document.querySelector("main");
  if (mainElement != undefined && mainElement != null) {
    mainElement.classList.add("display-none");
  }
};

const getWalletData = async (clientToken = false) => {
  resetUIWallet();
  // const idClient = new URLSearchParams(window.location.search).get("nit");
  initClientWallet(clientToken);

  let url = `${baseUrl}Invoice?token=${token}`;
  if (clientToken) {
    url = `${baseUrl}Invoice?token=${clientToken}`;
  }
  // const url = "../../controller/router.php";
  // const data = {
  //   id: idClient,
  //   case: "dataWallet",
  // };
  const table = document.querySelector(".table-tbody-2");
  let html = "";

  // const response = await postData(url, data, true);
  const invoices = await (
    await fetch(url, {
      headers: {
        accept: "application/json",
      },
    })
  ).json();

  if (invoices.status == 404) {
    showCardMessage();
    return;
  }

  document.querySelector("body main").classList.remove("display-none");
  invoices.map((invoice) => {

    let fechaString = invoice.FVence;

    let fecha = new Date(fechaString);

    let dia = fecha.getDate(); // Obtiene el día (del 1 al 31)
    let mes = fecha.getMonth() + 1; // Mes (0-11), se suma 1 para que sea del 1 al 12
    let año = fecha.getFullYear(); // Obtiene el año (cuatro dígitos)

    html += `
      <tr class="first-row-tbody">
        <td data-label="Factura" class="tbody-td">
        ${invoice.Consecutivo}
        </td>
        <td data-label="Pague antes de" class="tbody-td">
        ${dia}/${mes}/${año}
              </td>
        <td data-label="Saldo" class="tbody-td">
        ${new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(
      invoice.saldo
    )}
        </td>
        <td data-label="Días" class="tbody-td">
        ${invoice.DAtraso}
        </td>
      </tr>
    `;
  });
  // const name = formatName(clientName);
  table.innerHTML = html;
  const mainElement = document.querySelector("main");
  if (
    mainElement != undefined &&
    mainElement != null &&
    mainElement.classList.contains("display-none")
  ) {
    mainElement.classList.remove("display-none");
  }
};

const formatName = (fullName) => {
  const nameParts = fullName.split(" ");
  const formattedParts = nameParts.map(
    (part) => part.charAt(0).toUpperCase() + part.slice(1).toLowerCase()
  );
  return formattedParts.join(" ");
};

const getClients = async () => {
  // let response = { hasManyClients: false, client: null };
  const searchParams = new URLSearchParams(window.location.search);
  if (searchParams.has("clientSelected")) return true;
  const url = `${baseUrl}ContactClients?token=${searchParams.get("token")}`;

  const responseClients = await (
    await fetch(url, {
      headers: {
        accept: "application/json",
      },
    })
  ).json();
  // if (responseClients.length > 1) {
  selectClientDOM(responseClients, getWalletData);
  // response.hasManyClients = true;
  // }

  // response.client = responseClients[0].CLIENTE;
  // return response;
};

document.addEventListener("DOMContentLoaded", async () => {
  // getWalletUser();
  if (!urlQuery.has("bot")) {
    await getClients();
    return;
  }
  getWalletData();
});

const redirectPay = () => {
  window.location.href = "https://checkout.wompi.co/l/a47vPE";
};

document.querySelector("#pay-button").addEventListener("click", redirectPay);
