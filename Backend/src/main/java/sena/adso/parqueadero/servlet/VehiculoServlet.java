package sena.adso.parqueadero.servlet;

import sena.adso.parqueadero.dao.VehiculoDAO;
import sena.adso.parqueadero.model.Vehiculo;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.List;

/**
 * VehiculoServlet - API REST para gestión de vehículos
 * 
 * Rutas disponibles:
 *   GET    /api/vehiculos          → lista todos
 *   GET    /api/vehiculos/{id}     → busca por ID
 *   GET    /api/vehiculos?placa=XX → busca por placa
 *   POST   /api/vehiculos          → crea nuevo
 *   PUT    /api/vehiculos/{id}     → actualiza
 *   DELETE /api/vehiculos/{id}     → elimina
 */
public class VehiculoServlet extends HttpServlet {

    private final VehiculoDAO dao = new VehiculoDAO();

    // ─── GET ──────────────────────────────────────────────────────────────────
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {

        resp.setContentType("application/json;charset=UTF-8");
        PrintWriter out = resp.getWriter();

        try {
            String pathInfo = req.getPathInfo();    // ej: /5  o  null
            String placa    = req.getParameter("placa");

            if (placa != null && !placa.isEmpty()) {
                // Buscar por placa: GET /api/vehiculos?placa=ABC123
                Vehiculo v = dao.buscarPorPlaca(placa);
                if (v != null) {
                    out.print(v.toJson());
                } else {
                    resp.setStatus(HttpServletResponse.SC_NOT_FOUND);
                    out.print("{\"error\":\"Vehículo no encontrado\"}");
                }

            } else if (pathInfo == null || pathInfo.equals("/")) {
                // Listar todos: GET /api/vehiculos
                List<Vehiculo> lista = dao.listarTodos();
                StringBuilder sb = new StringBuilder("[");
                for (int i = 0; i < lista.size(); i++) {
                    if (i > 0) sb.append(",");
                    sb.append(lista.get(i).toJson());
                }
                sb.append("]");
                out.print(sb);

            } else {
                // Buscar por ID: GET /api/vehiculos/5
                int id = Integer.parseInt(pathInfo.substring(1));
                Vehiculo v = dao.buscarPorId(id);
                if (v != null) {
                    out.print(v.toJson());
                } else {
                    resp.setStatus(HttpServletResponse.SC_NOT_FOUND);
                    out.print("{\"error\":\"Vehículo no encontrado\"}");
                }
            }

        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.print("{\"error\":\"" + e.getMessage() + "\"}");
        }
    }

    // ─── POST ─────────────────────────────────────────────────────────────────
    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {

        resp.setContentType("application/json;charset=UTF-8");
        PrintWriter out = resp.getWriter();

        try {
            String body = leerBody(req);
            Vehiculo v = parsearVehiculo(body);
            boolean ok = dao.insertar(v);

            if (ok) {
                resp.setStatus(HttpServletResponse.SC_CREATED);
                out.print("{\"mensaje\":\"Vehículo registrado correctamente\"}");
            } else {
                resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
                out.print("{\"error\":\"No se pudo registrar el vehículo\"}");
            }

        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.print("{\"error\":\"" + e.getMessage() + "\"}");
        }
    }

    // ─── PUT ──────────────────────────────────────────────────────────────────
    @Override
    protected void doPut(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {

        resp.setContentType("application/json;charset=UTF-8");
        PrintWriter out = resp.getWriter();

        try {
            int id = Integer.parseInt(req.getPathInfo().substring(1));
            String body = leerBody(req);
            Vehiculo v = parsearVehiculo(body);
            v.setId(id);
            boolean ok = dao.actualizar(v);

            if (ok) {
                out.print("{\"mensaje\":\"Vehículo actualizado\"}");
            } else {
                resp.setStatus(HttpServletResponse.SC_NOT_FOUND);
                out.print("{\"error\":\"Vehículo no encontrado\"}");
            }

        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.print("{\"error\":\"" + e.getMessage() + "\"}");
        }
    }

    // ─── DELETE ───────────────────────────────────────────────────────────────
    @Override
    protected void doDelete(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {

        resp.setContentType("application/json;charset=UTF-8");
        PrintWriter out = resp.getWriter();

        try {
            int id = Integer.parseInt(req.getPathInfo().substring(1));
            boolean ok = dao.eliminar(id);

            if (ok) {
                out.print("{\"mensaje\":\"Vehículo eliminado\"}");
            } else {
                resp.setStatus(HttpServletResponse.SC_NOT_FOUND);
                out.print("{\"error\":\"Vehículo no encontrado\"}");
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
            while ((linea = br.readLine()) != null) sb.append(linea);
        }
        return sb.toString();
    }

    /**
     * Parser JSON manual (sin librerías externas)
     * Formato esperado: {"placa":"ABC123","tipo":"CARRO","propietario":"Juan","telefono":"3101234567"}
     */
    private Vehiculo parsearVehiculo(String json) {
        Vehiculo v = new Vehiculo();
        v.setPlaca(extraerValor(json, "placa"));
        v.setTipo(extraerValor(json, "tipo"));
        v.setPropietario(extraerValor(json, "propietario"));
        v.setTelefono(extraerValor(json, "telefono"));
        return v;
    }

    private String extraerValor(String json, String clave) {
        String patron = "\"" + clave + "\":\"";
        int inicio = json.indexOf(patron);
        if (inicio == -1) return "";
        inicio += patron.length();
        int fin = json.indexOf("\"", inicio);
        return json.substring(inicio, fin);
    }
}
