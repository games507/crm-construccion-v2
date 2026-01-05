// resources/js/islands/mount-existencias.js
import React from "react";
import { createRoot } from "react-dom/client";
import ExistenciasApp from "./existencias.jsx";

function mount() {
  const el = document.getElementById("existencias-react");
  if (!el) return;

  const apiUrl = el.dataset.apiUrl || "";
  const csrf =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  createRoot(el).render(<ExistenciasApp apiUrl={apiUrl} csrf={csrf} />);
}

mount();
