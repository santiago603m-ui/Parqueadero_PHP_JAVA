package sena.adso.parqueadero.servlet;

import sena.adso.parqueadero.dao.RegistroDAO;
import sena.adso.parqueadero.model.Registro;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.List;
import java.util.Map;

public class RegistroServlet extends HttpServlet {

    private final RegistroDAO dao = new RegistroDAO();

    // ─── GET ──────────────────────────────────────────────────────────────────
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {

        resp.setContentType("application/json;charset=UTF-8");
        PrintWriter out = resp.getWriter();

        String pathInfo = req.getPathInfo(); // null, "/" o "/reporte"

        if (pathInfo != null && pathInfo.equals("/reporte")) {
            try {
                Map<String, Object> reporte = dao.obtenerReporteDia();
                String json = String.format(
                        "{\"totalDia\":%.2f,\"cantidadSalidas\":%d,\"fecha\":\"%s\"}",
                        (Double) reporte.get("totalDia"),
                        (Integer) reporte.get("cantidadSalidas"),
                        (String) reporte.get("fecha"));
                out.print(json);
            } catch (Exception e) {
                resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
                out.print("{\"error\":\"" + e.getMessage() + "\"}");
            }
            return; // ← salir aquí; no continuar con la lógica de abajo
        }

        // Lógica original sin cambios
        try {
            String estado = req.getParameter("estado");
            List<Registro> lista;

            if ("FINALIZADO".equalsIgnoreCase(estado)) {
                lista = dao.listarHistorial();
            } else {
                lista = dao.listarActivos();
            }

            StringBuilder sb = new StringBuilder("[");
            for (int i = 0; i < lista.size(); i++) {
                if (i > 0)
                    sb.append(",");
                sb.append(lista.get(i).toJson());
            }
            sb.append("]");
            out.print(sb);

        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.print("{\"error\":\"" + e.getMessage() + "\"}");
        }
    }

    // ─── POST → Registrar ENTRADA ─────────────────────────────────────────────
    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {

        resp.setContentType("application/json;charset=UTF-8");
        PrintWriter out = resp.getWriter();

        try {
            String body = leerBody(req);
            int vehiculoId = Integer.parseInt(extraerValorNumerico(body, "vehiculoId"));
            int nuevoId = dao.registrarEntrada(vehiculoId);

            if (nuevoId > 0) {
                resp.setStatus(HttpServletResponse.SC_CREATED);
                out.print("{\"mensaje\":\"Entrada registrada\",\"registroId\":" + nuevoId + "}");
            } else {
                resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
                out.print("{\"error\":\"No se pudo registrar la entrada\"}");
            }

        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.print("{\"error\":\"" + e.getMessage() + "\"}");
        }
    }

    // ─── PUT → Registrar SALIDA /api/registros/{id}/salida ──────────────────
    @Override
    protected void doPut(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {

        resp.setContentType("application/json;charset=UTF-8");
        PrintWriter out = resp.getWriter();

        try {
            String path = req.getPathInfo(); // "/5/salida"
            String[] partes = path.split("/");
            int registroId = Integer.parseInt(partes[1]);

            Registro reg = dao.registrarSalida(registroId);

            if (reg != null) {
                out.print(reg.toJson());
            } else {
                resp.setStatus(HttpServletResponse.SC_NOT_FOUND);
                out.print("{\"error\":\"Registro no encontrado o ya finalizado\"}");
            }

        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.print("{\"error\":\"" + e.getMessage() + "\"}");
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private String leerBody(HttpServletRequest req) throws IOException {
        StringBuilder sb = new StringBuilder();
        try (BufferedReader br = req.getReader()) {
            String linea;
            while ((linea = br.readLine()) != null)
                sb.append(linea);
        }
        return sb.toString();
    }

    private String extraerValorNumerico(String json, String clave) {
        String patron = "\"" + clave + "\":";
        int inicio = json.indexOf(patron);
        if (inicio == -1)
            return "0";
        inicio += patron.length();
        int fin = inicio;
        while (fin < json.length() && (Character.isDigit(json.charAt(fin))))
            fin++;
        return json.substring(inicio, fin);
    }
}