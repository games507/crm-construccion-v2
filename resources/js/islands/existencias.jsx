import React, { useEffect, useMemo, useState } from "react";

function cx(...a) {
  return a.filter(Boolean).join(" ");
}

export default function ExistenciasApp({ apiUrl }) {
  const [rows, setRows] = useState([]);
  const [q, setQ] = useState("");
  const [almacen, setAlmacen] = useState("all");
  const [loading, setLoading] = useState(true);
  const [err, setErr] = useState("");

  const [sortKey, setSortKey] = useState("material");
  const [sortDir, setSortDir] = useState("asc");

  useEffect(() => {
    let alive = true;
    setLoading(true);
    setErr("");

    if (!apiUrl) {
      setErr("No se recibió apiUrl para cargar existencias.");
      setLoading(false);
      return;
    }

    fetch(apiUrl, { headers: { Accept: "application/json" } })
      .then(async (r) => {
        if (!r.ok) throw new Error(`Error ${r.status}`);
        return r.json();
      })
      .then((data) => {
        if (!alive) return;
        const arr = Array.isArray(data?.data)
          ? data.data
          : Array.isArray(data)
            ? data
            : [];
        setRows(arr);
      })
      .catch((e) => alive && setErr(e.message || "Error cargando"))
      .finally(() => alive && setLoading(false));

    return () => {
      alive = false;
    };
  }, [apiUrl]);

  const almacenes = useMemo(() => {
    const set = new Set();
    rows.forEach((r) => r.almacen && set.add(r.almacen));
    return ["all", ...Array.from(set).sort()];
  }, [rows]);

  const filtered = useMemo(() => {
    const qq = q.trim().toLowerCase();
    return rows.filter((r) => {
      if (almacen !== "all" && (r.almacen || "") !== almacen) return false;
      if (!qq) return true;
      const hay = [r.material, r.codigo, r.categoria, r.almacen]
        .filter(Boolean)
        .join(" ")
        .toLowerCase();
      return hay.includes(qq);
    });
  }, [rows, q, almacen]);

  const sorted = useMemo(() => {
    const copy = [...filtered];
    copy.sort((a, b) => {
      const av = a?.[sortKey] ?? "";
      const bv = b?.[sortKey] ?? "";

      const an = typeof av === "number" ? av : Number(av);
      const bn = typeof bv === "number" ? bv : Number(bv);

      let res;
      if (
        !Number.isNaN(an) &&
        !Number.isNaN(bn) &&
        (sortKey === "existencia" || sortKey === "minimo")
      ) {
        res = an - bn;
      } else {
        res = String(av).localeCompare(String(bv), "es", { sensitivity: "base" });
      }
      return sortDir === "asc" ? res : -res;
    });
    return copy;
  }, [filtered, sortKey, sortDir]);

  function toggleSort(key) {
    if (sortKey === key) setSortDir((d) => (d === "asc" ? "desc" : "asc"));
    else {
      setSortKey(key);
      setSortDir("asc");
    }
  }

  function badge(stock, minimo) {
    const s = Number(stock ?? 0);
    const m = Number(minimo ?? 0);

    if (s === 0) return { text: "Agotado", cls: "bg-slate-100 text-slate-700 border-slate-200" };
    if (m > 0 && s <= m) return { text: "Bajo", cls: "bg-rose-100 text-rose-700 border-rose-200" };
    return { text: "OK", cls: "bg-emerald-100 text-emerald-700 border-emerald-200" };
  }

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h2 className="text-xl font-black text-slate-900">Existencias</h2>
          <p className="text-sm font-semibold text-slate-500">
            Inventario actual por material y almacén
          </p>
        </div>

        <div className="flex flex-col sm:flex-row gap-2 sm:items-center">
          <input
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Buscar material, código, categoría…"
            className="w-full sm:w-[320px] rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-200"
          />

          <select
            value={almacen}
            onChange={(e) => setAlmacen(e.target.value)}
            className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-200"
          >
            {almacenes.map((a) => (
              <option key={a} value={a}>
                {a === "all" ? "Todos los almacenes" : a}
              </option>
            ))}
          </select>
        </div>
      </div>

      {/* Card */}
      <div className="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        {loading && (
          <div className="p-5 text-sm font-bold text-slate-500">
            Cargando existencias…
          </div>
        )}

        {!loading && err && (
          <div className="p-5 text-sm font-bold text-rose-700 bg-rose-50 border-t border-rose-100">
            {err}
          </div>
        )}

        {!loading && !err && (
          <>
            <div className="overflow-x-auto">
              <table className="min-w-full text-sm">
                <thead className="bg-slate-50 border-b border-slate-200">
                  <tr className="text-left text-slate-600">
                    {[
                      ["material", "Material"],
                      ["codigo", "Código"],
                      ["categoria", "Categoría"],
                      ["almacen", "Almacén"],
                      ["existencia", "Existencia"],
                      ["minimo", "Mínimo"],
                    ].map(([key, label]) => (
                      <th key={key} className="px-4 py-3 font-extrabold">
                        <button
                          className="inline-flex items-center gap-1 hover:text-slate-900"
                          onClick={() => toggleSort(key)}
                          type="button"
                        >
                          {label}
                          {sortKey === key && (
                            <span className="text-xs">{sortDir === "asc" ? "▲" : "▼"}</span>
                          )}
                        </button>
                      </th>
                    ))}
                    <th className="px-4 py-3 font-extrabold">Estado</th>
                  </tr>
                </thead>

                <tbody>
                  {sorted.length === 0 ? (
                    <tr>
                      <td className="px-4 py-5 font-bold text-slate-500" colSpan={7}>
                        No hay resultados para tu filtro.
                      </td>
                    </tr>
                  ) : (
                    sorted.map((r, i) => {
                      const b = badge(r.existencia, r.minimo);
                      return (
                        <tr
                          key={r.id ?? i}
                          className={cx(
                            "border-b border-slate-100",
                            i % 2 === 0 ? "bg-white" : "bg-slate-50/40"
                          )}
                        >
                          <td className="px-4 py-3 font-extrabold text-slate-900">
                            {r.material}
                          </td>
                          <td className="px-4 py-3 font-bold text-slate-700">
                            {r.codigo || "-"}
                          </td>
                          <td className="px-4 py-3 font-bold text-slate-700">
                            {r.categoria || "-"}
                          </td>
                          <td className="px-4 py-3 font-bold text-slate-700">
                            {r.almacen || "-"}
                          </td>
                          <td className="px-4 py-3 font-black text-slate-900">
                            {r.existencia ?? 0}
                          </td>
                          <td className="px-4 py-3 font-black text-slate-900">
                            {r.minimo ?? 0}
                          </td>
                          <td className="px-4 py-3">
                            <span
                              className={cx(
                                "inline-flex items-center rounded-full border px-2 py-1 text-xs font-black",
                                b.cls
                              )}
                            >
                              {b.text}
                            </span>
                          </td>
                        </tr>
                      );
                    })
                  )}
                </tbody>
              </table>
            </div>

            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 py-3 border-t border-slate-200 bg-white">
              <div className="text-xs font-bold text-slate-500">
                Registros: <span className="text-slate-900">{sorted.length}</span>
              </div>
              <div className="text-xs font-bold text-slate-500">
                Orden: <span className="text-slate-900">{sortKey}</span> ({sortDir})
              </div>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
