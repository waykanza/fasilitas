Imports System.Data.SqlClient
Imports System.IO
Imports Microsoft.Office.Interop
Imports System.Globalization

Module export_bca

    Sub Main()

        Dim app_path,
            conn_txt, status_txt, respon_txt,
            msg_respon As String

        msg_respon = ""

        app_path = My.Application.Info.DirectoryPath
        'app_path = "F:\UwAmp\www\pkb\vb\export\bca"

        conn_txt = Path.GetDirectoryName(Path.GetDirectoryName(app_path)) + "\conn.txt"
        status_txt = app_path + "\status.txt"
        respon_txt = app_path + "\respon.txt"

        If File.Exists(status_txt) = False Then
            File.AppendAllText(status_txt, "")
        End If
        If File.Exists(respon_txt) = False Then
            File.AppendAllText(respon_txt, "")
        End If
        If File.Exists(conn_txt) = False Then
            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(respon_txt, "File conn.txt tidak ditemukan! hubungi MSI !")
            Environment.Exit(0)
        End If

        ' ============================= SET ACTION =============================

        Dim conn As New SqlConnection
        Dim cmd As New SqlCommand
        Dim row As SqlDataReader

        Dim xls As Excel.Application
        Dim wb As Excel.Workbook
        Dim ws As Excel.Worksheet

        Dim time As DateTime = Date.Now()
        Dim periode As String = time.ToString("yyyyMM")
        Dim i As Integer = 1

        ' ============================= HERE WE GO =============================

        Try

            ' ============== STATUS & RESULT ==============

            File.WriteAllText(status_txt, "PROSES")
            File.WriteAllText(respon_txt, "")

            ' ============== EXCEL ==============

            xls = CreateObject("Excel.Application")
            wb = xls.Workbooks.Add()
            ws = wb.ActiveSheet

            With ws
                .Cells(i, 1).Value = "NO_PELANGGAN"
                .Cells(i, 2).Value = "KODE BLOK DAN NAMA PELANGGAN"
                .Cells(i, 3).Value = "ABONEMEN"
                .Cells(i, 4).Value = "AIR"
                .Cells(i, 5).Value = "IPL"
                .Cells(i, 6).Value = "DENDA"
            End With

            ' ============== DB ==============

            Dim conn_str = File.ReadAllText(conn_txt)
            conn = New SqlConnection(conn_str)
            cmd.Connection = conn
            conn.Open()

            cmd.Connection = conn
            cmd.CommandText = "" +
            "SELECT " +
                "b.NO_PELANGGAN, " +
                "(SELECT (KODE_BLOK + ' ' + NAMA_PELANGGAN) FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN) AS BLOK_NAPEL, " +
                "SUM(ISNULL(b.ABONEMEN,0)) AS ABONEMEN, " +
                "SUM(ISNULL(b.JUMLAH_AIR,0) - ISNULL(b.DISKON_RUPIAH_AIR,0)) AS JUMLAH_AIR, " +
                "SUM(ISNULL(b.JUMLAH_IPL,0) - ISNULL(b.DISKON_RUPIAH_IPL,0)) AS JUMLAH_IPL, " +
                "SUM(ISNULL(b.DENDA,0)) AS DENDA " +
            "FROM " +
                "KWT_PEMBAYARAN_AI b " +
            "WHERE " +
                "b.STATUS_BAYAR IS NULL " +
            "GROUP BY b.NO_PELANGGAN " +
            "ORDER BY BLOK_NAPEL "

            row = cmd.ExecuteReader()

            ' ============== CHECK FILE ==============

            Dim save_file_xls As String = app_path + "\files\BCA_EXPORT_" + periode + ".xls"

            ' ============== FILL DATA ==============

            Dim v(5) As String

            Do While row.Read()

                i = i + 1

                v(0) = "'" + row("NO_PELANGGAN")
                v(1) = row("BLOK_NAPEL")
                v(2) = row("ABONEMEN")
                v(3) = row("JUMLAH_AIR")
                v(4) = row("JUMLAH_IPL")
                v(5) = row("DENDA")

                ws.Range("A" + i.ToString, "F" + i.ToString).Value = v

            Loop

            row.Close()

            xls.Visible = True
            xls.UserControl = True
            ws.SaveAs(save_file_xls)
            wb.Close()

            wb = Nothing
            ws = Nothing
            xls.Quit()
            xls = Nothing

        Catch ex As NullReferenceException

            ' SKIP FOR THIS ERROR

        Catch ex As Exception

            msg_respon = ex.Message + vbNewLine

        Finally

            conn.Close()

            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(respon_txt, msg_respon)

            'Console.ReadLine()
            Environment.Exit(0)

        End Try

    End Sub

End Module
