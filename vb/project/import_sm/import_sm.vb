
Imports System.Data.SqlClient
Imports System.IO

Module import_sm

    Private Function clean(ByVal s As String) As String
        s = Trim(s.Replace("''", "``").Replace("'", "`"))
        If String.IsNullOrEmpty(s) Then
            Return ""
        Else
            Return s
        End If

    End Function

    Private Function is_empty(ByVal v As String, Optional ByVal r As String = "") As String
        If String.IsNullOrEmpty(v) Then
            Return clean(r)
        Else
            Return clean(v)
        End If

    End Function

    Private Function is_array_exists(ByVal a() As String, ByVal v As Integer, Optional ByVal r As String = "") As String
        If a.Length < (v + 1) Then
            Return r
        Else
            Return clean(a(v))
        End If

    End Function

    Private Function to_int(ByVal s As String) As Integer
        Dim i As Double

        If Double.TryParse(clean(s), i) Then
            Return CInt(i)
        Else
            Throw New Exception("Error format baris [Digit angka]'.")
        End If

    End Function

    Private Function max(ByVal v As Integer, ByVal r As Integer) As String
        If (v > r) Then
            Return v
        Else
            Return r
        End If

    End Function

    Private Function set_msg_total(ByVal s As Integer, ByVal e As Integer) As String

        Return "Total baris sukses: " + s.ToString + vbNewLine + "Total baris error: " + e.ToString + vbNewLine

    End Function


    Sub Main()

        Dim app_path,
            conn_txt, status_txt,
            upload_txt, respon_txt,
            msg_respon As String

        msg_respon = ""

        app_path = My.Application.Info.DirectoryPath
        'app_path = "F:\UwAmp\www\pkb\vb\import\sm"

        conn_txt = Path.GetDirectoryName(Path.GetDirectoryName(app_path)) + "\conn.txt"
        status_txt = app_path + "\status.txt"
        upload_txt = app_path + "\upload.txt"
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

        Dim conn As New SqlConnection
        Dim cmd As New SqlCommand
        Dim row As SqlDataReader
        Dim row_int As Integer

        Dim i, e, s As Integer

        i = 0
        e = 0
        s = 0

        Try

            ' SET STATUS AND CLEAN RESULT
            File.WriteAllText(status_txt, "PROSES")
            File.WriteAllText(respon_txt, "")

            If File.Exists(upload_txt) = False Then
                Throw New Exception("File import tidak ditemukan.")
            End If

            Dim conn_str = File.ReadAllText(conn_txt)
            conn = New SqlConnection(conn_str)
            cmd.Connection = conn
            conn.Open()

            Dim KEY_AIR, PERIODE, ID_PEMBAYARAN, NO_PELANGGAN, KETERANGAN As String

            Dim STAND_LALU, STAND_ANGKAT, STAND_AKHIR, PEMAKAIAN,
                LIMIT_BLOK1, LIMIT_BLOK2, LIMIT_BLOK3, LIMIT_BLOK4, LIMIT_STAND_MIN_PAKAI,
                BLOK1, BLOK2, BLOK3, BLOK4, STAND_MIN_PAKAI,
                TARIF1, TARIF2, TARIF3, TARIF4, TARIF_MIN_PAKAI,
                JUMLAH_AIR, ABONEMEN As Integer

            Dim sr As New StreamReader(upload_txt)
            Dim line, spl() As String

            Do While sr.Peek() >= 0

                i = i + 1
                line = sr.ReadLine()

                spl = Split(line, vbTab)

                PERIODE = is_array_exists(spl, 0)
                NO_PELANGGAN = is_array_exists(spl, 1)

                ' CHECK EXIST STRING
                If (PERIODE = "" Or NO_PELANGGAN = "") Then

                    e = e + 1

                    msg_respon = msg_respon + vbNewLine + "Baris: " + i.ToString + " | Error format baris. "

                    File.WriteAllText(respon_txt, set_msg_total(s, e) + msg_respon)

                    Continue Do

                End If

                PERIODE = PERIODE.Substring(2, 4) + PERIODE.Substring(0, 2)
                ID_PEMBAYARAN = "4#" + PERIODE + "#" + NO_PELANGGAN

                ' CHECK EXIST ROW

                cmd.CommandText = "SELECT COUNT(ID_PEMBAYARAN) AS ID_PEMBAYARAN FROM KWT_PEMBAYARAN_AI WHERE TRX IN ('1', '2', '4', '5') AND AKTIF_AIR = '1' AND ID_PEMBAYARAN = '" + ID_PEMBAYARAN + "' AND STATUS_BAYAR IS NULL"

                row_int = CInt(cmd.ExecuteScalar())

                If (row_int < 1) Then

                    e = e + 1

                    msg_respon = msg_respon + vbNewLine + "Baris: " + i.ToString + " | Pelanggan tidak terdaftar dalam rencana pembayaran. " + NO_PELANGGAN.ToString

                    File.WriteAllText(respon_txt, set_msg_total(s, e) + msg_respon)

                    Continue Do

                End If

                STAND_LALU = to_int(is_array_exists(spl, 2, "0"))
                STAND_AKHIR = to_int(is_array_exists(spl, 3, "0"))
                STAND_ANGKAT = to_int(is_array_exists(spl, 4, "0"))
                PEMAKAIAN = to_int(is_array_exists(spl, 5, "0"))
                KETERANGAN = is_array_exists(spl, 7)

                KEY_AIR = ""
                BLOK1 = 0
                BLOK2 = 0
                BLOK3 = 0
                BLOK4 = 0
                STAND_MIN_PAKAI = 0
                TARIF1 = 0
                TARIF2 = 0
                TARIF3 = 0
                TARIF4 = 0
                TARIF_MIN_PAKAI = 0
                ABONEMEN = 0

                ' GET TARIF

                cmd.CommandText = "" +
                "SELECT TOP 1 " +
                    "b.KEY_AIR, " +
                    "b.BLOK1, b.BLOK2, b.BLOK3, b.BLOK4, b.STAND_MIN_PAKAI, " +
                    "b.TARIF1, b.TARIF2, b.TARIF3, b.TARIF4, " +
                    "b.ABONEMEN " +
                "FROM " +
                    "KWT_PELANGGAN a " +
                    "LEFT JOIN KWT_TARIF_AIR b ON a.KEY_AIR = b.KEY_AIR " +
                "WHERE a.NO_PELANGGAN = '" + NO_PELANGGAN + "'"

                row = cmd.ExecuteReader()

                While row.Read()

                    KEY_AIR = is_empty(row("KEY_AIR"))
                    LIMIT_BLOK1 = to_int(row("BLOK1"))
                    LIMIT_BLOK1 = to_int(row("BLOK1"))
                    LIMIT_BLOK2 = to_int(row("BLOK2"))
                    LIMIT_BLOK3 = to_int(row("BLOK3"))
                    LIMIT_BLOK4 = to_int(row("BLOK4"))
                    LIMIT_STAND_MIN_PAKAI = to_int(row("STAND_MIN_PAKAI"))
                    TARIF1 = to_int(row("TARIF1"))
                    TARIF2 = to_int(row("TARIF2"))
                    TARIF3 = to_int(row("TARIF3"))
                    TARIF4 = to_int(row("TARIF4"))
                    ABONEMEN = to_int(row("ABONEMEN"))

                End While

                row.Close()

                ' CHECK EXIST KEY

                If (String.IsNullOrEmpty(KEY_AIR)) Then

                    e = e + 1

                    msg_respon = msg_respon + vbNewLine + "Baris: " + i.ToString + " | Key air tidak ditemukan. " + NO_PELANGGAN.ToString

                    File.WriteAllText(respon_txt, set_msg_total(s, e) + msg_respon)

                    Continue Do

                End If

                ' PROCESS VALUE

                If (PEMAKAIAN < LIMIT_STAND_MIN_PAKAI) Then

                    BLOK1 = PEMAKAIAN
                    STAND_MIN_PAKAI = LIMIT_STAND_MIN_PAKAI - BLOK1
                    TARIF_MIN_PAKAI = TARIF1

                Else

                    If (PEMAKAIAN > LIMIT_BLOK1) Then

                        BLOK1 = LIMIT_BLOK1
                        PEMAKAIAN = PEMAKAIAN - BLOK1

                        If (PEMAKAIAN > LIMIT_BLOK2) Then

                            BLOK2 = LIMIT_BLOK2
                            PEMAKAIAN = PEMAKAIAN - BLOK2

                            If (PEMAKAIAN > LIMIT_BLOK3) Then

                                BLOK3 = LIMIT_BLOK3
                                PEMAKAIAN = PEMAKAIAN - BLOK3
                                BLOK4 = max(0, PEMAKAIAN)

                            Else
                                BLOK3 = max(0, PEMAKAIAN)
                            End If
                        Else
                            BLOK2 = max(0, PEMAKAIAN)
                        End If
                    Else
                        BLOK1 = max(0, PEMAKAIAN)
                    End If
                End If

                JUMLAH_AIR = (BLOK1 * TARIF1) + (BLOK2 * TARIF2) + (BLOK3 * TARIF3) + (BLOK4 * TARIF4) + (STAND_MIN_PAKAI * TARIF_MIN_PAKAI)

                ' UPDATE STAND METER

                cmd.CommandText =
                        "UPDATE KWT_PEMBAYARAN_AI SET" +
                            " STAND_ANGKAT = " + STAND_ANGKAT.ToString +
                            " ,STAND_AKHIR = " + STAND_AKHIR.ToString +
                            " ,STAND_LALU = " + STAND_LALU.ToString +
                            " ,BLOK1 = " + BLOK1.ToString +
                            " ,BLOK2 = " + BLOK2.ToString +
                            " ,BLOK3 = " + BLOK3.ToString +
                            " ,BLOK4 = " + BLOK4.ToString +
                            " ,STAND_MIN_PAKAI = " + STAND_MIN_PAKAI.ToString +
                            " ,TARIF1 = " + TARIF1.ToString +
                            " ,TARIF2 = " + TARIF2.ToString +
                            " ,TARIF3 = " + TARIF3.ToString +
                            " ,TARIF4 = " + TARIF4.ToString +
                            " ,TARIF_MIN_PAKAI = " + TARIF_MIN_PAKAI.ToString +
                            " ,ABONEMEN = " + ABONEMEN.ToString +
                            " ,JUMLAH_AIR = " + JUMLAH_AIR.ToString +
                        " WHERE TRX IN ('1', '2', '4', '5') AND AKTIF_AIR = '1' AND ID_PEMBAYARAN = '" + ID_PEMBAYARAN + "'"

                cmd.ExecuteNonQuery()
                s = s + 1
                'Console.Clear()
                'Console.Write(s)

            Loop

            sr.Close()

        Catch ex As NullReferenceException

            ' SKIP FOR THIS ERROR

        Catch ex As Exception

            e = e + 1

            msg_respon = msg_respon + vbNewLine + "Baris: " + i.ToString + " | Error format baris. | " + ex.Message

        Finally

            conn.Close()

            File.WriteAllText(respon_txt, set_msg_total(s, e) + msg_respon)

            File.WriteAllText(status_txt, "FINISH")
            'Console.ReadLine()
            Environment.Exit(0)

        End Try

    End Sub

End Module
