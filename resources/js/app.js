import "./bootstrap";
import Alpine from "alpinejs";

window.Alpine = Alpine;

// Registrar TODO antes de Alpine.start()
document.addEventListener("alpine:init", () => {
  /**
   * Store global para sidebar (persistido)
   */
  Alpine.store("sidebar", {
    collapsed: false,

    init() {
      try {
        const v = localStorage.getItem("sidebar_collapsed");
        this.collapsed = v === "1";
      } catch (e) {
        this.collapsed = false;
      }
    },

    toggle() {
      this.collapsed = !this.collapsed;
      try {
        localStorage.setItem("sidebar_collapsed", this.collapsed ? "1" : "0");
      } catch (e) {}
    },
  });

  /**
   * Componente del menú (open groups)
   */
  Alpine.data("sidebarMenu", () => ({
    open: {
      inventario: false,
      config: false,
    },

    init() {
      // Inicializa store siempre al cargar componente
      Alpine.store("sidebar").init();

      // auto-open según ruta actual
      const path = (window.location.pathname || "").replace(/^\/+|\/+$/g, "");

      if (path.startsWith("inventario")) this.open.inventario = true;
      if (path.startsWith("admin")) this.open.config = true;
    },

    toggleGroup(key) {
      if (Alpine.store("sidebar").collapsed) return;
      this.open[key] = !this.open[key];
    },
  }));
});

Alpine.start();
