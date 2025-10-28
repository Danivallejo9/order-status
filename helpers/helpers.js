export const selectClientDOM = (clients, activateFn) => {
  // const mainElement = document.querySelector("main");
  // // if (!mainElement.classList.contains("display-none")) {
  // //   mainElement.classList.add("display-none");
  // // }
  const body = document.querySelector("body");
  const div = document.createElement("div");
  const divLabel = document.createElement("div");
  const divSelect = document.createElement("div");
  divSelect.className = "select";
  div.className = "select-client";
  const label = document.createElement("label");
  const select = document.createElement("select");

  if (clients.length > 1) {
    label.textContent = "Seleccione una sucursal";
    const option = document.createElement("option");
    option.value = 0;
    option.text = "SELECCIONE...";
    select.append(option);
    // Add Event
    select.addEventListener("change", (e) => {
      if (e.target.value == 0) {
        if (document.querySelector("#section-2-id")) {
          document.querySelector("#section-2-id").classList.add("display-none");
          document.querySelector("#section-1-id").classList.add("display-none");
        }
        return;
      }
      activateFn(e.target.value);
    });
  } else {
    label.textContent = "Cliente";
    activateFn(clients[0].CLIENTE);
  }

  clients.map((client) => {
    const option = document.createElement("option");
    option.value = client.CLIENTE;
    option.text = client.NOMBRE;
    select.append(option);
  });

  divLabel.append(label);
  div.append(divLabel);
  divSelect.append(select);
  div.append(divSelect);
  body.prepend(div);
};

export const postData = async (url = "", data = {}, json = true) => {
  const response = await fetch(url, {
    method: "POST",
    mode: "cors",
    cache: "no-cache",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
    },
    redirect: "follow",
    referrerPolicy: "no-referrer",
    body: JSON.stringify(data),
  });
  if (json == true) {
    return response.json();
  } else {
    return response;
  }
};

export const numberFormat = (number) => {
  if (number == undefined) return 0;
  return new Intl.NumberFormat(undefined, {
    maximumFractionDigits: 0,
  }).format(number);
};

export const divLoading = (site, state = true) => {
  const ubication = document.querySelector(`#${site}`);
  if (state) {
    const centerDiv = document.createElement("div");
    centerDiv.id = "div-loader";
    centerDiv.className = "center-div";
    const divLoader = document.createElement("div");
    divLoader.className = "lds-dual-ring";
    centerDiv.append(divLoader);
    ubication.append(centerDiv);
  }

  if (!state) {
    document.querySelector("#div-loader").remove();
  }
};

export const resetUI = (data) => {
  data.map((element) => {
    switch (element.type) {
      case "HTML":
        element.target.innerHTML = "";
        break;
    }
  });
};
