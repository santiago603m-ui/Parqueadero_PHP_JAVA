package sena.adso.parqueadero.dao;

import sena.adso.parqueadero.model.Vehiculo;
import sena.adso.parqueadero.util.ConexionDB;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

/**
 * DAO Vehiculo - Patrón DAO para acceso a datos
 * Parqueadero Boyacá - SENA CIMM ADSO
 */
public class VehiculoDAO {

    // ─── CREATE ────────────────────────────────────────────────────────────────
    public boolean insertar(Vehiculo v) throws SQLException {
        String sql = "INSERT INTO vehiculos (placa, tipo, propietario, telefono) VALUES (?, ?, ?, ?)";
        try (Connection con = ConexionDB.getConexion();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, v.getPlaca());
            ps.setString(2, v.getTipo());
            ps.setString(3, v.getPropietario());
            ps.setString(4, v.getTelefono());
            return ps.executeUpdate() > 0;
        }
    }

    // ─── READ ALL ──────────────────────────────────────────────────────────────
    public List<Vehiculo> listarTodos() throws SQLException {
        List<Vehiculo> lista = new ArrayList<>();
        String sql = "SELECT * FROM vehiculos ORDER BY placa";
        try (Connection con = ConexionDB.getConexion();
             Statement st = con.createStatement();
             ResultSet rs = st.executeQuery(sql)) {
            while (rs.next()) {
                lista.add(mapear(rs));
            }
        }
        return lista;
    }

    // ─── READ BY ID ────────────────────────────────────────────────────────────
    public Vehiculo buscarPorId(int id) throws SQLException {
        String sql = "SELECT * FROM vehiculos WHERE id = ?";
        try (Connection con = ConexionDB.getConexion();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next() ? mapear(rs) : null;
            }
        }
    }

    // ─── READ BY PLACA ─────────────────────────────────────────────────────────
    public Vehiculo buscarPorPlaca(String placa) throws SQLException {
        String sql = "SELECT * FROM vehiculos WHERE placa = ?";
        try (Connection con = ConexionDB.getConexion();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, placa.toUpperCase());
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next() ? mapear(rs) : null;
            }
        }
    }

    // ─── UPDATE ────────────────────────────────────────────────────────────────
    public boolean actualizar(Vehiculo v) throws SQLException {
        String sql = "UPDATE vehiculos SET placa=?, tipo=?, propietario=?, telefono=? WHERE id=?";
        try (Connection con = ConexionDB.getConexion();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, v.getPlaca());
            ps.setString(2, v.getTipo());
            ps.setString(3, v.getPropietario());
            ps.setString(4, v.getTelefono());
            ps.setInt(5, v.getId());
            return ps.executeUpdate() > 0;
        }
    }

    // ─── DELETE ────────────────────────────────────────────────────────────────
    public boolean eliminar(int id) throws SQLException {
        String sql = "DELETE FROM vehiculos WHERE id = ?";
        try (Connection con = ConexionDB.getConexion();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, id);
            return ps.executeUpdate() > 0;
        }
    }

    // ─── MAPEO ResultSet → Vehiculo ────────────────────────────────────────────
    private Vehiculo mapear(ResultSet rs) throws SQLException {
        return new Vehiculo(
            rs.getInt("id"),
            rs.getString("placa"),
            rs.getString("tipo"),
            rs.getString("propietario"),
            rs.getString("telefono")
        );
    }
}
