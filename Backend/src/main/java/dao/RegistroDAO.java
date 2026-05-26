package dao;

import java.sql.*;
import util.ConexionDB;

public class RegistroDAO {

    public boolean registrarEntrada(int vehiculoId) {
        String sql = "INSERT INTO registros (vehiculo_id, entrada, estado) VALUES (?, NOW(), 'ACTIVO')";
        try (Connection conn = ConexionDB.getConexion();
                PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, vehiculoId);
            return pstmt.executeUpdate() > 0;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean registrarSalida(int registroId) {
        String sqlSelect = "SELECT r.entrada, v.tipo FROM registros r JOIN vehiculos v ON r.vehiculo_id = v.id WHERE r.id = ?";
        String sqlUpdate = "UPDATE registros SET salida = NOW(), tarifa = ?, estado = 'FINALIZADO' WHERE id = ?";

        try (Connection conn = ConexionDB.getConexion();
                PreparedStatement pstmtSelect = conn.prepareStatement(sqlSelect)) {

            pstmtSelect.setInt(1, registroId);
            ResultSet rs = pstmtSelect.executeQuery();

            if (rs.next()) {
                Timestamp entrada = rs.getTimestamp("entrada");
                String tipo = rs.getString("tipo");

                long milisegundos = System.currentTimeMillis() - entrada.getTime();
                long minutos = milisegundos / 60000;

                double horas = Math.max(1, Math.ceil(minutos / 60.0));

                double tarifaHora = 0;
                switch (tipo) {
                    case "CARRO":
                        tarifaHora = 3000;
                        break;
                    case "MOTO":
                        tarifaHora = 1500;
                        break;
                    case "CAMION":
                        tarifaHora = 5000;
                        break;
                }

                double tarifaFinal = horas * tarifaHora;

                try (PreparedStatement pstmtUpdate = conn.prepareStatement(sqlUpdate)) {
                    pstmtUpdate.setDouble(1, tarifaFinal);
                    pstmtUpdate.setInt(2, registroId);
                    return pstmtUpdate.executeUpdate() > 0;
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return false;
    }
}