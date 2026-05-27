package sena.adso.parqueadero.dao;

import sena.adso.parqueadero.model.Registro;
import sena.adso.parqueadero.util.ConexionDB;

import java.sql.*;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class RegistroDAO {

    // Tarifas por hora en COP
    private static final double TARIFA_CARRO = 3000.0;
    private static final double TARIFA_MOTO = 1500.0;
    private static final double TARIFA_CAMION = 5000.0;

    // ─── Registrar ENTRADA ─────────────────────────────────────────────────────
    public int registrarEntrada(int vehiculoId) throws SQLException {
        String ahora = LocalDateTime.now().format(java.time.format.DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss"));
        String sql = "INSERT INTO registros (vehiculo_id, entrada, estado) VALUES (?, ?, 'ACTIVO')";
        try (Connection con = ConexionDB.getConexion();
                PreparedStatement ps = con.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            ps.setInt(1, vehiculoId);
            ps.setString(2, ahora);
            ps.executeUpdate();
            try (ResultSet rs = ps.getGeneratedKeys()) {
                return rs.next() ? rs.getInt(1) : -1;
            }
        }
    }

    // ─── Registrar SALIDA y calcular tarifa ────────────────────────────────────
    public Registro registrarSalida(int registroId) throws SQLException {
        // 1. Obtener datos del registro activo
        Registro reg = buscarPorId(registroId);
        if (reg == null || !"ACTIVO".equals(reg.getEstado()))
            return null;

        // 2. Calcular tarifa según tiempo transcurrido
        LocalDateTime ahora = LocalDateTime.now();
        long minutos = java.time.Duration.between(reg.getEntrada(), ahora).toMinutes();
        long horasCompletas = Math.max(1, (long) Math.ceil(minutos / 60.0)); // mínimo 1 hora
        double tarifaHora = getTarifaPorTipo(reg.getTipo());
        double total = horasCompletas * tarifaHora;

        // 3. Actualizar en BD
        String sql = "UPDATE registros SET salida=NOW(), tarifa=?, estado='FINALIZADO' WHERE id=?";
        try (Connection con = ConexionDB.getConexion();
                PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setDouble(1, total);
            ps.setInt(2, registroId);
            ps.executeUpdate();
        }

        // 4. Retornar registro actualizado
        return buscarPorId(registroId);
    }

    // ─── Listar registros ACTIVOS (vehículos dentro) ──────────────────────────
    public List<Registro> listarActivos() throws SQLException {
        return listarPorEstado("ACTIVO");
    }

    // ─── Listar HISTORIAL (finalizados) ───────────────────────────────────────
    public List<Registro> listarHistorial() throws SQLException {
        return listarPorEstado("FINALIZADO");
    }

    private List<Registro> listarPorEstado(String estado) throws SQLException {
        List<Registro> lista = new ArrayList<>();
        String sql = "SELECT r.*, v.placa, v.tipo FROM registros r " +
                "JOIN vehiculos v ON r.vehiculo_id = v.id " +
                "WHERE r.estado = ? ORDER BY r.entrada DESC";
        try (Connection con = ConexionDB.getConexion();
                PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, estado);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next())
                    lista.add(mapear(rs));
            }
        }
        return lista;
    }

    // ─── Buscar por ID ────────────────────────────────────────────────────────
    public Registro buscarPorId(int id) throws SQLException {
        String sql = "SELECT r.*, v.placa, v.tipo FROM registros r " +
                "JOIN vehiculos v ON r.vehiculo_id = v.id WHERE r.id = ?";
        try (Connection con = ConexionDB.getConexion();
                PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next() ? mapear(rs) : null;
            }
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private double getTarifaPorTipo(String tipo) {
        if (tipo == null)
            return TARIFA_CARRO;
        switch (tipo.toUpperCase()) {
            case "MOTO":
                return TARIFA_MOTO;
            case "CAMION":
                return TARIFA_CAMION;
            default:
                return TARIFA_CARRO;
        }
    }

    private Registro mapear(ResultSet rs) throws SQLException {
        Registro r = new Registro();
        r.setId(rs.getInt("id"));
        r.setVehiculoId(rs.getInt("vehiculo_id"));
        r.setPlaca(rs.getString("placa"));
        r.setTipo(rs.getString("tipo"));
        r.setEntrada(rs.getTimestamp("entrada").toLocalDateTime());
        Timestamp sal = rs.getTimestamp("salida");
        if (sal != null)
            r.setSalida(sal.toLocalDateTime());
        r.setTarifa(rs.getDouble("tarifa"));
        r.setEstado(rs.getString("estado"));
        return r;
    }

    public Map<String, Object> obtenerReporteDia() throws SQLException {
        // Consulta SQL: suma tarifas y cuenta salidas del día actual
        String sql = "SELECT " +
                "  COALESCE(SUM(tarifa), 0) AS totalDia, " +
                "  COUNT(*) AS cantidadSalidas, " +
                "  CURDATE() AS fecha " +
                "FROM registros " +
                "WHERE DATE(salida) = CURDATE() " +
                "  AND estado = 'FINALIZADO'";

        Map<String, Object> reporte = new HashMap<>();

        try (Connection conn = ConexionDB.getConexion();
                PreparedStatement ps = conn.prepareStatement(sql);
                ResultSet rs = ps.executeQuery()) {

            if (rs.next()) {
                // Extraer valores del ResultSet y poblar el mapa
                reporte.put("totalDia", rs.getDouble("totalDia"));
                reporte.put("cantidadSalidas", rs.getInt("cantidadSalidas"));
                reporte.put("fecha", rs.getString("fecha"));
            } else {
                // Si no hay resultados, retornar valores en cero
                reporte.put("totalDia", 0.0);
                reporte.put("cantidadSalidas", 0);
                reporte.put("fecha", java.time.LocalDate.now().toString());
            }
        }
        return reporte;
    }
}
