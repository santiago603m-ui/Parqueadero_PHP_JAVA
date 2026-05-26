package servlet;

import dao.RegistroDAO;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.*;
import java.io.BufferedReader;
import java.io.IOException;

@WebServlet("/api/registros/*")
public class RegistroServlet extends HttpServlet {
    private RegistroDAO registroDAO = new RegistroDAO();

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        resp.setContentType("application/json");

        BufferedReader reader = req.getReader();
        String json = reader.readLine();

        int vehiculoId = Integer.parseInt(json.replaceAll("\\D", ""));

        if (registroDAO.registrarEntrada(vehiculoId)) {
            resp.setStatus(HttpServletResponse.SC_CREATED);
            resp.getWriter().write("{\"mensaje\":\"Entrada registrada correctamente\"}");
        } else {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            resp.getWriter().write("{\"error\":\"Error al registrar entrada\"}");
        }
    }

    @Override
    protected void doPut(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        resp.setContentType("application/json");
        String pathInfo = req.getPathInfo();

        if (pathInfo != null && pathInfo.endsWith("/salida")) {
            String[] splits = pathInfo.split("/");
            int registroId = Integer.parseInt(splits[1]);

            if (registroDAO.registrarSalida(registroId)) {
                resp.setStatus(HttpServletResponse.SC_OK);
                resp.getWriter().write("{\"mensaje\":\"Salida registrada y tarifa calculada\"}");
            } else {
                resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
                resp.getWriter().write("{\"error\":\"Error al procesar la salida\"}");
            }
        }
    }
}